// JavaScript Document
// both are not include '/' at the end
var serverHost = "http://192.168.0.190/NoSQLSystem";
var webRoot = "http://192.168.0.190/NoSQLSystem";

var requireLoginPage = "login.html";
var afterLoginPage = "main-menu.html";

var theme = "B";
// D = Default
// B = Bootstrap
// U = Uikit
// W = w3c
// J = jQueryUI

var CookiesEffectivePath = '/';

var directiveEditMode = {
	None: 0,
	NUll: 1,
	
	Create: 5,
	Amend: 6,
	Delete: 7,
	View: 8,
	AmendAndDelete: 9,
	ImportExport: 10,
	Import: 11,
	Export: 12,
	
	Copy: 15
}
var reservedPath = {
	controller: 'controller/',
	templateFolder: 'Templates/',
	screenTemplate: 'screen/',
	uiThemeTemplate: 'theme/',
}

app.constant('config', {
	serverHost: serverHost,
	webRoot: webRoot,
	requireLoginPage: requireLoginPage,
	afterLoginPage: afterLoginPage,
	
	uiTheme: theme,
	
	editMode: directiveEditMode,
	reservedPath: reservedPath,
	CookiesEffectivePath: CookiesEffectivePath,
    
    debugLog: {
        AllLogging: false,
        PageRecordsLimitDefault: true,
        LockControl: true,
        UnlockControl: true,
        TableStructureObtained: true,
        ShowCallStack: false
    }
});

app.config(['config', '$httpProvider', function(config, $httpProvider) {
	config.test = "Keith";
	
    delete $httpProvider.defaults.headers.common['X-Requested-With'];
    $httpProvider.defaults.headers.post['Accept'] = 'application/json, text/javascript';
    $httpProvider.defaults.headers.post['Content-Type'] = 'application/json; charset=utf-8';
    // $httpProvider.defaults.headers.post['Access-Control-Max-Age'] = '1728000';
    // $httpProvider.defaults.headers.common['Access-Control-Max-Age'] = '1728000';
    $httpProvider.defaults.headers.common['Accept'] = 'application/json, text/javascript';
    $httpProvider.defaults.headers.common['Content-Type'] = 'application/json; charset=utf-8';
    $httpProvider.defaults.useXDomain = true;
}]);