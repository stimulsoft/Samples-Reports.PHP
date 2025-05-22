StiHandler.prototype.process = function (args, callback) {
    if (args) {
        if (args.event === 'OpenReport' || args.event === 'EndProcessData')
            return null;

        if (args.event === 'BeginProcessData') {
            if (!this.databases.includes(args.database))
                return null;

            if (this.isFileDataAdapter(args) && !this.allowFileDataAdapters)
                return null;

            args.preventDefault = true;
        }

        if (callback)
            args.async = true;

        let command = {};
        for (let p in args) {
            switch (p) {
                case 'report':
                    // When requesting data, the report is not required on the server side
                    if (args.report && args.event !== 'BeginProcessData')
                        command.report = args.report.isRendered ? args.report.saveDocumentToJsonString() : args.report.saveToJsonString();
                    break;

                case 'settings':
                    if (args.settings) {
                        command.settings = JSON.stringify(args.settings);
                        command.reportType = typeof args.settings.is == 'function' && args.settings.is(Stimulsoft.Report.Dashboard.Export.IStiDashboardExportSettings) ? 2 : 1;
                    }
                    break;

                case 'data':
                    command.data = Stimulsoft.System.Convert.toBase64String(args.data);
                    break;

                case 'variables':
                    command[p] = this.getVariables(args[p]);
                    break;

                case 'viewer':
                    break;

                default:
                    command[p] = args[p];
                    break;
            }
        }

        let sendText = Stimulsoft.Report.Dictionary.StiSqlAdapterService.encodeCommand(command);
        let handlerCallback = function (data) {
            let success = typeof data == 'string' || typeof data == 'object' && data.success;
            if (callback && success && Stimulsoft.handler.isFileDataAdapter(args)) {

                // For file data, only string data should be passed to the callback function
                // If a JSON response was returned - the data was not loaded, try loading it using JavaScript
                args.preventDefault = typeof data == 'string';
                return callback(args.preventDefault ? data : null);
            }

            if (data.report) args.report = data.report;
            if (data.settings) Stimulsoft.handler.copySettings(data.settings, args.settings);
            if (data.pageRange) Stimulsoft.handler.copySettings(data.pageRange, args.pageRange);
            if (data.fileName) args.fileName = data.fileName;

            // The test connection form has its own error message form
            if (args.command != 'TestConnection' && !Stimulsoft.System.StiString.isNullOrEmpty(data.notice))
                setTimeout(function () {
                    Stimulsoft.System.StiError.showError(data.notice, true, data.success);
                }, 150);

            if (callback)
                return callback(data);
        }

        Stimulsoft.handler.send(sendText, handlerCallback);
    }
}

StiHandler.prototype.send = function (data, callback) {
    let request = new XMLHttpRequest();
    try {
        request.open('post', this.url, true);
        request.setRequestHeader('Cache-Control', 'max-age=0, no-cache, no-store, must-revalidate');
        request.setRequestHeader('Pragma', 'no-cache');

        if (this.cookie)
            request.setRequestHeader('Cookie', this.cookie);

        if (this.csrfToken) {
            request.setRequestHeader('X-CSRFToken', this.csrfToken);
            request.setRequestHeader('X-CSRF-Token', this.csrfToken);
        }

        request.timeout = this.timeout * 1000;
        request.onload = function () {
            if (request.status === 200) {
                let contentType = request.getResponseHeader('Content-Type');
                let resultType = request.getResponseHeader('X-Stimulsoft-Result'); // Success, Error, File, SQL, Variables
                let responseText = request.responseText;
                request.abort();

                if (Stimulsoft.handler.isFileResult(contentType, resultType))
                    return callback(responseText);

                try {
                    let args = Stimulsoft.Report.Dictionary.StiSqlAdapterService.decodeCommandResult(responseText);
                    if (args.report) {
                        let json = args.report;
                        args.report = new Stimulsoft.Report.StiReport();
                        args.report.load(json);
                    }

                    return callback(args);
                }
                catch (e) {
                    let message = typeof e == 'string' ? e : e.message;
                    Stimulsoft.System.StiError.showError(message);
                }
            } else {
                Stimulsoft.System.StiError.showError('Server response error: [' + request.status + '] ' + request.statusText);
            }
        };
        request.onerror = function (e) {
            let errorMessage = 'Connect to remote error: [' + request.status + '] ' + request.statusText;
            Stimulsoft.System.StiError.showError(errorMessage);
        };
        request.send(data);
    }
    catch (e) {
        let errorMessage = 'Connect to remote error: ' + e.message;
        Stimulsoft.System.StiError.showError(errorMessage);
        request.abort();
    }
}

StiHandler.prototype.https = function (data, callback) {
    let uri = require('url').parse(this.url);
    let options = {
        host: uri.hostname,
        port: uri.port,
        path:  uri.path,
        method: 'POST',
        timeout: this.timeout * 1000,
        headers: {
            'Cache-Control': 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma': 'no-cache',
            'Cookie': this.cookie,
            'X-CSRFToken': this.csrfToken,
            'X-CSRF-Token': this.csrfToken,
            'X-NodeJS-Id': this.nodejsId
        }
    }

    let responseText = '';
    let module = uri.protocol.replace(':', '');
    let request = require(module).request(options, function (response) {

        response.on('data', function (buffer) {
            responseText += buffer;
        });

        response.on('end', function () {
            try {
                let contentType = response.headers['content-type'];
                let resultType = response.headers['x-stimulsoft-result'];

                if (Stimulsoft.handler.isFileResult(contentType, resultType))
                    return callback(responseText);

                let args = Stimulsoft.Report.Dictionary.StiSqlAdapterService.decodeCommandResult(responseText);
                if (args.report) {
                    let json = args.report;
                    args.report = new Stimulsoft.Report.StiReport();
                    args.report.load(json);
                }

                callback(args);
            }
            catch (e) {
                let message = typeof e == 'string' ? e : e.message;
                console.log('ResponseError: ' + message);
                console.log(responseText);
                process.exit(1);
            }
        });
    });

    request.on('error', function (e) {
        console.log('RequestError: ' + e.message);
        process.exit(1);
    });

    request.on('timeout', function () {
        console.log('RequestError: Timeout ' + this.timeout + 'ms');
        process.exit(2);
    });

    request.write(data);
    request.end();
}

StiHandler.prototype.setOptions = function () {
    Stimulsoft.Report.StiOptions.WebServer.timeout = this.timeout;
    Stimulsoft.Report.StiOptions.WebServer.encryptData = this.encryptData;
    Stimulsoft.Report.StiOptions.WebServer.passQueryParametersToReport = this.passQueryParametersToReport;
    Stimulsoft.Report.StiOptions.WebServer.checkDataAdaptersVersion = this.checkDataAdaptersVersion;
    Stimulsoft.Report.StiOptions.Engine.escapeQueryParameters = this.escapeQueryParameters;
}

StiHandler.prototype.isNullOrEmpty = function (value) {
    return value == null || value === '' || value === undefined;
}

StiHandler.prototype.isFileDataAdapter = function (args) {
    return args.command === 'GetSchema' || args.command === 'GetData';
}

StiHandler.prototype.isFileResult = function (contentType, resultType) {
    return typeof contentType == 'string' && !contentType.startsWith('application/json') || resultType == 'File';
}

StiHandler.prototype.getVariables = function (variables) {
    if (variables) {
        for (let variable of variables) {
            if (variable.type === 'DateTime' && variable.value != null)
                variable.value = variable.value.toString('YYYY-MM-DD HH:mm:ss');
        }
    }

    return variables;
}

StiHandler.prototype.getCookie = function (name) {
    if (typeof document == 'undefined') return '';
    let matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
    return matches ? decodeURIComponent(matches[1]) : '';
}

StiHandler.prototype.copySettings = function (from, to) {
    for (let key in from) {
        if (to.hasOwnProperty(key) && typeof to[key] != 'function') {
            if (key == 'encoding') to[key] = eval(from[key]);
            else if (key == 'pageRange') Stimulsoft.handler.copySettings(from[key], to[key]);
            else if (typeof to[key] != 'object' && typeof from[key] == typeof to[key]) to[key] = from[key];
        }
    }
}

// For the Node.js engine, take the parameters from the event handler URL
StiHandler.prototype.getUrlParametersNodejs = function () {
    let parameters = [];

    if (this.handler && this.handler.url)
        this.handler.url.replace(/[?&]+([^=&]+)=([^&]*)/gi, (m, key, value) => parameters.push({
            name: key,
            value: decodeURI(value)
        }).toString());

    return parameters;
}

StiHandler.prototype.setFunctions = function () {
    // 1: Client JavaScript
    // 2: Server Node.js
    if (this.engineType == 2) {
        Stimulsoft.System.IO.Http.handler = this;
        Stimulsoft.System.IO.Http.getUrlParameters = this.getUrlParametersNodejs;
    }
}

function StiHandler() {
    this.url = {url};
    this.timeout = {timeout};
    this.encryptData = {encryptData};
    this.passQueryParametersToReport = {passQueryParametersToReport};
    this.checkDataAdaptersVersion = {checkDataAdaptersVersion};
    this.escapeQueryParameters = {escapeQueryParameters};
    this.databases = {databases};
    this.frameworkType = {framework};
    this.cookie = {cookie};
    this.csrfToken = {csrfToken} || this.getCookie('csrftoken');
    this.allowFileDataAdapters = {allowFileDataAdapters};
    this.nodejsId = {nodejsId};
    this.engineType = {engineType};
    this.setOptions();
    this.setFunctions();
}

setTimeout(function () {
    let stimulsoft = Stimulsoft || {};
    stimulsoft.handler = new StiHandler();
})
