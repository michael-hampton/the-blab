function convert_youtube (input)
{
    var pattern = /(?:http?s?:\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=)?(\S+)/g;
    if (pattern.test (input))
    {
        var replacement = '<iframe width="420" height="345" src="http://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>';
        var input = input.replace (pattern, replacement);

        // For start time, turn get param & into ?
        var input = input.replace ('&amp;t=', '?t=');

    }
    return input;
}

function convert_vimeo (input)
{
    var pattern = /(?:http?s?:\/\/)?(?:www\.)?(?:vimeo\.com)\/?(\S+)/g;
    if (pattern.test (input))
    {
        var replacement = '<iframe width="420" height="345" src="//player.vimeo.com/video/$1" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
        var input = input.replace (pattern, replacement);
    }
    return input;
}

function convert_twitch (input)
{
    var pattern = /(?:http?s?:\/\/)?(?:www\.)?(?:twitch\.tv)\/?(\S+)/g;
    if (pattern.test (input))
    {
        var replacement = '<iframe src="https://player.twitch.tv/?channel=$1&!autoplay" frameborder="0" allowfullscreen="true" scrolling="no" height="378" width="620"></iframe>';
        var input = input.replace (pattern, replacement);
    }
    return input;
}

function convert_vocaroo (input)
{
    var pattern = /(?:http?s?:\/\/)?(?:www\.)?(?:vocaroo\.com\/i)\/?(\S+)/g;
    if (pattern.test (input))
    {
        var replacement = '<object width="148" height="44"><param name="movie" value="http://vocaroo.com/player.swf?playMediaID=$1&autoplay=0"></param><param name="wmode" value="transparent"></param><embed src="http://vocaroo.com/player.swf?playMediaID=$1&autoplay=0" width="148" height="44" wmode="transparent" type="application/x-shockwave-flash"></embed></object>';
        var input = input.replace (pattern, replacement);
    }
    return input;
}

function convert_video_url (input)
{
    var pattern = /([-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?(?:webm|mp4|ogv))/gi;
    if (pattern.test (input))
    {
        var replacement = '<video controls="" loop="" controls src="$1" style="max-width: 960px; max-height: 676px;"></video>';
        var input = input.replace (pattern, replacement);
    }
    return input;
}

function convert_image_url (input)
{
    var pattern = /([-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?(?:jpg|jpeg|gif|png))/gi;
    if (pattern.test (input))
    {
        var replacement = '<a href="$1" target="_blank"><img class="sml" src="$1" /></a><br />';
        var input = input.replace (pattern, replacement);
    }
    return input;
}

// Run this function last
function convert_general_url (input)
{
    // Ignore " to not conflict with other converts
    var pattern = /(?!.*")([-a-zA-Z0-9@:%_\+.~#?&//=;]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=;]*))/gi;
    var res = pattern.test (input);
    var res2 = input.match (pattern);

    if (res)
    {
        var replacement = '<a href="' + input + '" target="_blank">$1</a>';
        //var input = input.replace (pattern, replacement);
    }
    return input;
}

function buildVideos (content)
{
    content = convert_general_url (content);
    content = convert_image_url (content);
    content = convert_video_url (content);
    content = convert_vocaroo (content);
    content = convert_twitch (content);
    content = convert_vimeo (content);
    content = convert_youtube (content);

    return content;
}