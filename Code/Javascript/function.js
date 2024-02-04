var topButtonClicked = null;
var leftButtonClicked = null;
function LoadPage(pageName, success = null) {
    if (topButtonClicked != null) {
        topButtonClicked.classList.remove("active");
    }
    if (leftButtonClicked != null) {
        leftButtonClicked.classList.remove("active");
    }
    topButtonClicked = document.getElementById("top" + pageName);
    leftButtonClicked = document.getElementById("left" + pageName);
    topButtonClicked.classList.add("active");
    leftButtonClicked.classList.add("active");
    if (getCookie("userID") != "") {
        document.getElementById("topUser").innerHTML = "欢迎！" + getCookie("userName");
        document.getElementById("leftUser").innerHTML = "欢迎！" + getCookie("userName");
    }
    if (pageName == "") {
        pageName = "Main"
    }

    loadDate = document.getElementById("loadDate");
    loadDate.style.display = "flex";
    setTimeout(() => {
        AjaxRequest("./Core/Function/GetPage.php?name=" + pageName, function (result) {
            loadDate.style.display = "none";
            document.getElementById("Main").innerHTML = result;
            history.replaceState(null, "", "#" + pageName);
            if (success) {
                setTimeout(() => { success(result) });
            }
        }, null, "GET", false);
    }, 0);
    return true;
}
function LoadChart(id, settingArray) {
    const data = {
        labels: settingArray['labels'],
        datasets: [{
            label: settingArray['id'],
            data: settingArray['size'],
            backgroundColor: settingArray['background'],
            hoverOffset: 4
        }]
    };
    const options = {
        plugins: {
            title: {
                display: true,
                text: settingArray['title'],
            }
        }
    }
    const config = {
        type: 'pie',
        data: data,
        options: options
    };
    const canvas = document.getElementById(id);

    new Chart(canvas, config);
}

function ArrayDate(value, recursion = false, word = ";") {
    var Number = 0;
    var TempNumberValue = value;
    do {
        Number++;
        TempNumberValue = TempNumberValue.replace(word, "");
    } while (TempNumberValue.indexOf(word) != -1);
    var Line = new Array;
    var startLine = 0;
    var tempLineValue = "";
    for (let index = 0; index <= Number; index++) {
        var tempLineEnd = value.indexOf(word);
        if (tempLineEnd == -1) {
            tempLineValue = value.substr(startLine);
        } else {
            tempLineValue = value.substring(startLine, tempLineEnd);
        }
        if (tempLineValue != "") {
            Line[index] = tempLineValue;
        }
        startLine = tempLineEnd;
        value = value.replace(word, "");
    }
    if (recursion == true) {
        return Line;
    }
    var LineDate = new Array;
    var i = 0;
    Line.forEach(element => {
        TempNumberValue = element;
        do {
            TempNumberValue = TempNumberValue.replace("[", "");
        } while (TempNumberValue.indexOf("[") != -1);
        var temp = ArrayDate(TempNumberValue, true, "]");
        LineDate[i] = {};
        temp.forEach(elements => {
            var tempKey = elements.substr(0, elements.indexOf(":"));
            var tempValue = elements.substr(elements.indexOf(":") + 1);
            LineDate[i][tempKey] = tempValue;
        })
        i++;
    });
    return LineDate;
}

function CloseWindow() {
    var windows = document.getElementById("windows");
    windows.style.display = "none";
}