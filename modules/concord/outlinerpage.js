var appConsts = {
    "productname": "WikiOutliner",
    "productnameForDisplay": "WikiOutliner, Part of Haoji KMS Project",
    "domain": "kms.haojixu.me",
    "version": "0.52"
}
var appPrefs = {
    "outlineFont": "Georgia", "outlineFontSize": 16, "outlineLineHeight": 24,
    "authorName": "", "authorEmail": ""
};

var currentPage = ""

var whenLastKeystroke = new Date (), whenLastAutoSave = new Date ();
var flReadOnly = false, flRenderMode = false;
var cmdKeyPrefix = "Ctrl+";

var urlConcordSource = "https://raw.github.com/scripting/concord/master/opml/concord.opml";
var urlConcordCssSource = "https://raw.github.com/scripting/concord/master/opml/concordCss.opml";
var urlConcordDocs = "https://raw.github.com/scripting/concord/master/opml/concordDocs.opml";
var urlConcordUtilsSource = "https://raw.github.com/scripting/concord/master/opml/concordUtils.opml";
var urlHelloOutliner = "https://raw.github.com/scripting/concord/master/example1/source.opml";
var urlExample0 = "https://raw.github.com/scripting/concord/master/example0/source.opml";
var urlExample2 = "https://static.smallpicture.com/tacoma/wo/admin/2013/09/18/archive056.opml";
var urlWorknotes = "https://static.smallpicture.com/tacoma/wo/admin/2013/09/18/archive057.opml";

function initLocalStorage () {
    if (localStorage.savedOpmltext == undefined) {
        localStorage.savedOpmltext = initialOpmltext;
        editSource (urlConcordDocs); //9/14/13 by DW
    }
    if (localStorage.ctOpmlSaves == undefined) {
        localStorage.ctOpmlSaves = 0;
    }
    if (localStorage.whenLastSave == undefined) {
        localStorage.whenLastSave = new Date ().toString ();
    }
    if (localStorage.flTextMode == undefined) {
        localStorage.flTextMode = "true";
    }
    var onlineFile = loadOutlineFromCloud(currentPage)
    localStorage.savedOpmltext = onlineFile;
    console.log(localStorage.savedOpmltext)
}
function setInclude () { //used to test includes
    opSetOneAtt ("type", "include");
    opSetOneAtt ("url", "https://smallpicture.com/states.opml");
}
function editSource (url) {
    opXmlToOutline (initialOpmltext); //empty the outline display
    readText (url, function (opmltext, op) {
        opXmlToOutline (opmltext);
        saveOutlineNow ();
    }, undefined, true);
}
function nukeDom () {
    var summit, htmltext = "", indentlevel = 0;
    $(defaultUtilsOutliner).concord ().op.visitToSummit (function (headline) {
        summit = headline;
        return (true);
    });
    var visitSub = function (sub) {
        if (sub.attributes.getOne ("isComment") != "true") {
            htmltext += filledString ("\t", indentlevel) + sub.getLineText () + "\r\n"
            if (sub.countSubs () > 0) {
                indentlevel++;
                sub.visitLevel (visitSub);
                indentlevel--;
            }
        }
    };
    summit.visitLevel (visitSub);

    var t = new Object ();
    t.text = summit.getLineText ();
    htmltext = multipleReplaceAll (htmltext, t, false, "<" + "%", "%" + ">");

    document.open ();
    document.write (htmltext);
    document.close ();
}
function opExpandCallback (parent) {
    var type = parent.attributes.getOne ("type"), url = parent.attributes.getOne ("url"), xmlUrl = parent.attributes.getOne ("xmlUrl");
    //link nodes
    if ((type == "link") && (url != undefined)) {
        window.open (url);
        return;
    }
    //rss nodes
    if ((type == "rss") && (xmlUrl != undefined)) {
        window.open (xmlUrl);
        return;
    }
    //include nodes
    if ((type == "include") && (url != undefined)) {
        parent.deleteSubs ();
        parent.clearChanged ();
        readText (url, function (opmltext, op) {
            op.insertXml (opmltext, right);
            op.clearChanged ();
        }, parent, true);
    }
}
function opInsertCallback (headline) {
    headline.attributes.setOne ("created", new Date ().toUTCString ());
}
function opCollapseCallback (parent) {
    if (parent.attributes.getOne ("type") == "include") {
        parent.deleteSubs ();
        parent.clearChanged ();
    }
}
function opHoverCallback (headline) {
    var atts = headline.attributes.getAll (), s = "";
    //set cursor to pointer if there's a url attribute -- 3/24/13  by DW
    if ((atts.url != undefined) || (atts.xmlUrl != undefined)) {
        document.body.style.cursor = "pointer";
    }
    else {
        document.body.style.cursor = "default";
    }
}
function opCursorMovedCallback (headline) {
}
function opKeystrokeCallback (event) {
    whenLastKeystroke = new Date ();
}
function runSelection () {
    var value = eval (opGetLineText ());
    opDeleteSubs ();
    opInsert (value, "right");
    opGo ("left", 1);
}
function setOutlinerPrefs (id, flRenderMode, flReadonly) {
    $(id).concord ({
        "prefs": {
            "outlineFont": appPrefs.outlineFont,
            "outlineFontSize": appPrefs.outlineFontSize,
            "outlineLineHeight": appPrefs.outlineLineHeight,
            "renderMode": flRenderMode,
            "readonly": flReadonly,
            "typeIcons": appTypeIcons
        },
        "callbacks": {
            "opInsert": opInsertCallback,
            "opCursorMoved": opCursorMovedCallback,
            "opExpand": opExpandCallback,
            "opHover": opHoverCallback,
            "opKeystroke": opKeystrokeCallback
        }
    });
}

function getEditToken (editfilename) {
    // Here was once a XMLHttp request to get the token. Now I think that was stupid.
    //Get Modernized
    return mw.user.tokens.get( 'editToken' )
}

function saveOutlineToCloud( pageName, content ) {
    var api = new mw.Api();
    api.postWithToken( "edit", {
        action: "edit",
        title: pageName,
        summary: "Updated via Outliner at " + new Date().toString(),
        text: content
    } ).done( function( result, jqXHR ) {
        console.log( "Saved successfully" );
    } ).fail( function( code, result ) {
        if ( code === "http" ) {
            console.log( "HTTP error: " + result.textStatus ); // result.xhr contains the jqXHR object
        } else if ( code === "ok-but-empty" ) {
            console.log( "Got an empty response from the server" );
        } else {
            console.log( "API error: " + code );
        }
    } );
}

function loadOutlineFromCloud (pageName) {
    // Dooooon't do anythign before outline is loaded from MediaWiki
    var request = new XMLHttpRequest();
    request.open('GET', '/index.php?title=Outline:'+pageName+'&action=raw', false);
    request.setRequestHeader("Is-Raw", 333)
    request.send(null);

    if (request.status === 200) {
        return request.responseText;
    } else {
        localStorage.savedOpmltext = initialOpmltext;
        editSource (urlConcordDocs); //9/14/13 by DW
        localStorage.ctOpmlSaves = 0;
        localStorage.whenLastSave = new Date ().toString ();
        localStorage.flTextMode = "true";
        $( "#filename" ).val(pageName);
        currentPage = pageName;
        return initialOpmltext;
    }
}

function saveOutlineNow () {
    localStorage.savedOpmltext = opOutlineToXml (appPrefs.authorName, appPrefs.authorEmail);
    localStorage.ctOpmlSaves++;
    opClearChanged ();
    console.log ("saveOutlineNow: " + localStorage.savedOpmltext.length + " chars.");
    //sync to cloud (Up emoji)
    var editfilename = "Outline:" + currentPage;
    saveOutlineToCloud(editfilename, localStorage.savedOpmltext);

}

function backgroundProcess () {
    if (opHasChanged ()) {
        if (secondsSince (whenLastKeystroke) >= 1) {
            saveOutlineNow ();
        }
    }
}

function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    location.search
        .substr(1)
        .split("&")
        .forEach(function (item) {
            tmp = item.split("=");
            if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
        });
    return result;
}

function requestAndSetFileList() {
    var api = new mw.Api();
    var promiser = api.parse(
        new mw.Title( 'Outlines' ),
        { section: 1 }
    );
    promiser.then( function( text ) {
        $("#filelist").html(text)
    } );
}

function startup () {
    // First and Foremost, get page name by checking query
    var urlPage = findGetParameter("pagename")
    if (urlPage === null) urlPage = "MasterOutline"
    $("#filename").val(urlPage);

    currentPage = $("#filename").val();

    // Initiate sidebar (File list)
    requestAndSetFileList();

    initLocalStorage()
    $("#idMenuProductName").text (appConsts.productname);
    $("#idProductVersion").text ("v" + appConsts.version);
    //init menu keystrokes
    if (navigator.platform.toLowerCase ().substr (0, 3) == "mac") {
        cmdKeyPrefix = "&#8984;";
    }
    $("#idMenubar .dropdown-menu li").each (function () {
        var li = $(this);
        var liContent = li.html ();
        liContent = liContent.replace ("Cmd-", cmdKeyPrefix);
        li.html (liContent);
    });
    setOutlinerPrefs ("#outliner", true, false); //9/20/13 by DW -- change initial value for renderMode from false to true
    opSetFont (appPrefs.outlineFont, appPrefs.outlineFontSize, appPrefs.outlineLineHeight);
    opXmlToOutline (localStorage.savedOpmltext);
    self.setInterval (function () {backgroundProcess ()}, 1000); //call every second
}

$(this).on('keyup', function(event) {
    if (event.keyCode == 27) {
        toggleFullscreen()
    } else {
        console.log(event.keyCode)
    }
})

$(document).ready (function () {
    startup ();
});