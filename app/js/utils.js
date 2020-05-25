function scroll(buffer, source, num_elements)
{
    if(window.scrollY+window.innerHeight >= (document.body.scrollHeight-300))
        return source.slice(0, buffer.length + num_elements-1);
    return buffer;
}

function getMonthName(i) 
{
    switch(i) {
        case 1: return "Gennaio";
        case 2: return "Febbraio";
        case 3: return "Marzo";
        case 4: return "Aprile";
        case 5: return "Maggio";
        case 6: return "Giugno";
        case 7: return "Luglio";
        case 8: return "Agosto";
        case 9: return "Settembre";
        case 10: return "Ottobre";
        case 11: return "Novembre";
        case 12: return "Dicembre";
    }
    return "";
}

// taglia del testo senza tagliare le parole
function truncate(text, len, trunc_word = false) {
    var trimmed_text = text.substring(0, len);
    if(trunc_word) return trimmed_text;
    return trimmed_text.substring(
        0,
        Math.min(trimmed_text.length, trimmed_text.lastIndexOf(" "))
    );
}

function dateFormat(data, short)
{
    var actual_time = new Date();
    var actual_millis = actual_time.getTime();
    var date_format = new Date(data);
    var diff = actual_millis - date_format.getTime();

    if(diff < 60000) {
        return Math.floor(diff/1000) + (short ? "s" : " secondi fa");
    }
    else if(diff < 3600000) {
        var minuti = Math.floor(diff/60000);
        return minuti + (short ? "m" : ((minuti < 2) ? " minuto fa" : " minuti fa"));
    }
    else if(diff < 86400000) {
        var ore = Math.floor(diff/3600000);
        return ore + (short ? "h" : ((ore < 2) ? " ora fa" : " ore fa"));
    }
    else if(short) {
        var days = Math.floor(diff/86400000);
        if(days < 7)
            return days + "d";
        return Math.floor(days/7) + "w";
    }
    else if(diff < 172800000) {
        return "Ieri";
    }
    else if(date_format.getFullYear() == actual_time.getFullYear()) {
        return date_format.getDate() + " " + this.getMonthName(date_format.getMonth() + 1);
    }
    return date_format.getDate() + " " + this.getMonthName(date_format.getMonth() + 1) + ", " + date_format.getFullYear();
}

// Per la preview dei video
function setVideoPreviewSize(video)
{
    if(!video) return;
    if(video.videoWidth >= video.videoHeight) {
        video.style.height = "100%";
        video.style.width = "auto";
        video.style.top = "0";
        video.style.left = "50%";
        video.style.transform = "translateX(-50%)";
    }
    else { 
        video.style.height = "auto";
        video.style.width = "100%";
        video.style.left = "0";
        video.style.top = "50%";
        video.style.transform = "translateY(-50%)";
    }
}

function get(url)
{
    return new Promise((resolve, reject) => $.get(url).done(resolve).fail(reject));
}

function post(url, data)
{
    return new Promise((resolve, reject) => $.post(url, data).done(resolve).fail(reject));
}

function post_multipart(settings)
{
    return new Promise((resolve, reject) => $.post(settings).done(resolve).fail(reject));
}