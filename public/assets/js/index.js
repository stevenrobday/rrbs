var tag = document.createElement('script');

tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

var player;

function onYouTubeIframeAPIReady() {
    $.get({url: '/video/playVideo', dataType: 'json'})
        .done(function (res) {
            if (res.e) {
                player = new YT.Player('player', {
                    videoId: res.videoId,
                    width: '0',
                    height: '0',
                    playerVars: {
                        'playsinline': 1,
                        'autoplay': 1,
                        'controls': 1,
                        'start': res.s,
                        'end': res.e,
                        'fs': 1,
                        'rel': 0,
                        'enablejsapi': 1,
                        'origin': 'https://rrbs.org'
                    },
                    events: {
                        'onReady': onPlayerReady,
                        'onStateChange': onPlayerStateChange
                    }
                });
            } else {
                player = new YT.Player('player', {
                    videoId: res.videoId,
                    width: '0',
                    height: '0',
                    playerVars: {
                        'playsinline': 1,
                        'autoplay': 1,
                        'controls': 1,
                        'start': res.s,
                        'fs': 1,
                        'rel': 0,
                        'enablejsapi': 1,
                        'origin': 'https://rrbs.org'
                    },
                    events: {
                        'onReady': onPlayerReady,
                        'onStateChange': onPlayerStateChange
                    }
                });
            }
        });
}

let windowWidth = window.innerWidth;
let windowHeight = window.innerHeight - $('#navbar').outerHeight();
let playerEl;

function onPlayerReady(event) {
    playerEl = $('#player');
    playerEl.hide();
    resizePlayer();
}

function onPlayerStateChange(e) {
    if (e.data == YT.PlayerState.ENDED) {
        player.stopVideo();
        loadVideo(e);
    }
}

function loadVideo(e) {
    $.get({url: '/video/playVideo', dataType: 'json'})
        .done(function (res) {
            if (res.e)
            {
                if (res.e > res.s)
                {
                    e.target.loadVideoById({
                        'videoId': res.videoId,
                        'startSeconds': res.s,
                        'endSeconds': res.e
                    });
                }
                else loadVideo(e);
            }
            else e.target.loadVideoById(res.videoId, res.s);
        });
}

$(window).resize(function () {
    playerEl.hide();
    playerEl.width(0);
    playerEl.height(0);
    windowWidth = window.innerWidth;
    windowHeight = window.innerHeight - $('#navbar').outerHeight();
    resizePlayer();
});

function resizePlayer() {
    let windowRatio = windowWidth / windowHeight;

    playerEl.width(640);
    playerEl.height(360);

    let playerRatio = 640 / 360;

    let newPlayerWidth;
    let newPlayerHeight;

    if (windowRatio < playerRatio) {
        newPlayerWidth = windowWidth;
        newPlayerHeight = 360 * windowWidth / 640;
    } else {
        newPlayerWidth = 640 * windowHeight / 360;
        newPlayerHeight = windowHeight;
    }

    let playerContainer = $('#playerContainer');
    playerContainer.height(windowHeight);

    playerEl.width(newPlayerWidth);
    playerEl.height(newPlayerHeight);
    playerEl.show();
}
