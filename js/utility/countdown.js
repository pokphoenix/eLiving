
// var initial = 500;
// var count = initial;
// var counter; //10 will  run it every 100th of a second

function timer()
{
    if (count <= 0) {
        clearInterval(counter);
        return;
    }
    count--;
    displayCount(count);
}

function displayCount(count)
{
    var res = count / 100;
    document.getElementById("timer").innerHTML = res.toPrecision(count.toString().length) + " secs";
}

$('#timer_start').on('click', function () {
    clearInterval(counter);
    counter = setInterval(timer, 10);
});

$('#timer_stop').on('click', function () {
    clearInterval(counter);
});

$('#timer_reset').on('click', function () {
    clearInterval(counter);
    count = initial;
    displayCount(count);
});

// displayCount(initial);
