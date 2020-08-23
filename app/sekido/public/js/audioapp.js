document.addEventListener(
    "DOMContentLoaded",
    () => {
        let baseurl_tweet = "https://twitter.com/intent/tweet?text=%23NowPlaying+";

        function set_info(tag) {
            $("#audio-info").text(
                $("ol li.playing a .info").text()
                    ? $("ol li.playing a .info").text()
                    : ""
            );
            $("#playing_title").text(
                $("ol li.playing a .info .title").text()
                    ? $("ol li.playing a .info .title").text()
                    : ""
            );
            $("#playing_thumbnail").attr('src',
                $("ol li.playing a img").attr('src')
                    ? $("ol li.playing a img").attr('src')
                    : ""
            );
            $("#twitter_share").attr(
                "href",
                baseurl_tweet +
                ($("ol li.playing a .info").text()
                    ? encodeURIComponent($("ol li.playing a .info").text())
                    : "")
            );
        }

        // Setup the player to autoplay the next track
        var a = audiojs.createAll({
            trackEnded: function () {
                var next = $("ol li.playing").next();
                if (!next.length) next = $("ol li").first();
                next.addClass("playing").siblings().removeClass("playing");
                audio.load($("a", next).attr("data-src"));
                audio.play();
                set_info($("a", next));
            },
        });

        // Load in the first track
        var audio = a[0];
        var first = $("ol a.musicitem").attr("data-src");
        $("ol li").first().addClass("playing");
        audio.load(first);
        set_info($("ol a"));

        // Load in a track on click
        $("ol li a.musicitem").click(function (e) {
            e.preventDefault();
            $(this).parent().addClass("playing").siblings().removeClass("playing");
            audio.load($(this).attr("data-src"));
            audio.play();
            set_info($(this));
        });
        // Keyboard shortcuts
        $(document).keydown(function (e) {
            var unicode = e.charCode ? e.charCode : e.keyCode;
            // right arrow
            if (unicode == 39) {
                var next = $("li.playing").next();
                if (!next.length) next = $("ol li").first();
                next.find("a.musicitem").click();
                // back arrow
            } else if (unicode == 37) {
                var prev = $("li.playing").prev();
                if (!prev.length) prev = $("ol li").last();
                prev.find("a.musicitem").click();
                // spacebar
            } else if (unicode == 32) {
                audio.playPause();
            }
        });

        $("button.queue").click(function (e) {
            add_to_queue(
                $(this).parent().find("a.musicitem").attr("id"),
                $(this).parent().html()
            );
        });
    },
    false
);


function add_to_queue(music_id, datasrc) {
    key = "queue";

    datalist = JSON.parse(localStorage.getItem(key));
    console.log(datalist);
    if (datalist === null) {
        datalist = {};
    }
    datalist[music_id] = datasrc;
    localStorage.setItem(key, JSON.stringify(datalist));

    return true;
}

function remove_from_queue(music_id) {
    key = "queue";

    datalist = JSON.parse(localStorage.getItem(key));
    console.log(datalist);
    delete datalist[music_id];
    localStorage.setItem(key, JSON.stringify(datalist));

    return true;
}
