var billPage = 0;
var billNowUrl = "";
var ShowAddTipsSetTimeOut = null;
function ShowTime(object) {
    var startTime = document.getElementById("startTime");
    var endTime = document.getElementById("endTime");
    if (object.value == "3" || object.value == "2") {
        startTime.style.display = "";
        endTime.style.display = "";
    } else {
        startTime.style.display = "none";
        endTime.style.display = "none";
    }
}

function topMainClick() {
    if (getCookie("userID") == "") {
        return false;
    }
    AjaxRequest("./Core/Bill/getMouthMoney.php", function (result) {
        var date = new Array;
        if (result == "") {
            date[0] = {};
            date[0]["income"] = "0";
            date[0]["pay"] = "0";
        } else {
            date = ArrayDate(result);
        }
        //console.log(date);
        var income = Number(date[0]["income"]) * 1000;
        var pay = Number(date[0]["pay"]) * 1000;
        var all = income - pay;
        document.getElementById("IncomeMouthDate").innerHTML = String(Math.floor(income) / 1000);
        document.getElementById("PayOutMouthDate").innerHTML = String(Math.floor(pay) / 1000);
        document.getElementById("MoneyMouthDate").innerHTML = String(Math.floor(all) / 1000);
        document.getElementById("userName").innerHTML = getCookie("userName");

        var time = new Date();
        var year = time.getFullYear();
        var mouth = time.getMonth() + 1;
        document.getElementById("NowMouth").innerHTML = year.toString() + "年" + mouth.toString() + "月";
    });

    AjaxRequest("./Core/Bill/getAllMoney.php", function (result) {
        var date = new Array;
        if (result == "") {
            date[0] = {};
            date[0]["income"] = "0";
            date[0]["pay"] = "0";
            date[0]["liabilities"] = "0";
            date[0]["lending"] = "0";
        } else {
            date = ArrayDate(result);
        }
        var income = Number(date[0]["income"]) * 1000;
        var pay = Number(date[0]["pay"]) * 1000;
        var liabilities = Number(date[0]["liabilities"]);
        var lending = Number(date[0]["lending"]);
        var all = income - pay;
        document.getElementById("LendingDate").innerHTML = lending;
        document.getElementById("LiabilitiesDate").innerHTML = liabilities;
        document.getElementById("IncomeDate").innerHTML = String(Math.floor(income) / 1000);
        document.getElementById("PayOutDate").innerHTML = String(Math.floor(pay) / 1000);
        document.getElementById("MoneyDate").innerHTML = String(Math.floor(all + (liabilities) * 1000) / 1000);
    });

    AjaxRequest("./Core/Bill/getDayMoney.php", function (result) {
        var date = new Array;
        if (result == "") {
            date[0] = {};
            date[0]["income"] = "0";
            date[0]["pay"] = "0";
        } else {
            date = ArrayDate(result);
        }
        var income = Number(date[0]["income"]) * 1000;
        var pay = Number(date[0]["pay"]) * 1000;
        var all = income - pay;
        document.getElementById("PayOutDateDay").innerHTML = String(Math.floor(pay) / 1000);
        document.getElementById("IncomeDateDay").innerHTML = String(Math.floor(income) / 1000);
        document.getElementById("MoneyMainDay").innerHTML = String(Math.floor(all) / 1000);
    });

    AjaxRequest("./Core/Bill/getTypeBillMoney.php", function (result) {
        var date = new Array;
        if (result == "") {
            document.getElementById("MouthPayMoneyDataDiv").innerHTML = "<span class='fs-2 text-black-50 pt-5 pb-5'>本月暂无消费记录</span>";
            document.getElementById("MouthIncomeMoneyDataDiv").innerHTML = "<span class='fs-2 text-black-50 pt-5 pb-5'>本月暂无收入记录</span>";
            return false;
        }
        date = ArrayDate(result);
        SetChartAndLoad(date, "MouthPayMoneyData", "0", "rgb(255, 0, 0)", "本月消费", "pay", "<span class='fs-2 text-black-50 pt-5 pb-5'>本月暂无消费记录</span>", "MouthPayMoneyDataDiv");
        SetChartAndLoad(date, "MouthIncomeMoneyData", "1", "rgb(0, 128, 0)", "本月收入", "income", "<span class='fs-2 text-black-50 pt-5 pb-5'>本月暂无收入记录</span>", "MouthIncomeMoneyDataDiv");
    });
}

function SetChartAndLoad(array, id, $type, color, title, name, error, element) {
    var allPayNumber = 0;
    array.forEach(elements => {
        if (elements['type'] == $type) {
            allPayNumber += Number(elements["money"]);
        }
    });

    var chartArray = new Array;
    var chartArrayLabels = new Array;
    var chartArrayBackground = new Array;
    var chartArraySize = new Array;
    var dateExist = false;
    var location = color.indexOf(",");
    var red = Number(color.substring(4, location));
    color = color.replace(",", "");
    var green = Number(color.substring(location, color.indexOf(",")));
    location = color.indexOf(",");
    color = color.replace(",", "");
    var blue = Number(color.substring(location, color.indexOf(")")));
    var i = 0;
    array.forEach(elements => {
        if (elements['type'] == $type) {
            chartArrayLabels.push(decodeURIComponent(elements["name"]));
            if (green >= 0 && green < 255) {
                green += 15;
            }
            if (green == 255) {
                green = 0;
            }
            if (red >= 0 && red < 255) {
                red += 15;
            }
            if (green >= 255) {
                red = 0;
            }
            if (blue >= 0 && blue < 255) {
                blue -= 15;
            }
            if (green >= 255) {
                blue = 0;
            }
            if (green <= 0) {
                blue = 255;
            }
            chartArrayBackground.push("rgb(" + red + "," + green + "," + blue + ")");
            //console.log("rgb(" + red + "," + green + "," + blue + ")");
            chartArraySize.push(Number(elements["money"]));
            dateExist = true;
            i++;
        }
    });
    if (dateExist == false) {
        document.getElementById(element).innerHTML = error;
        return false;
    }
    chartArray['labels'] = chartArrayLabels;
    chartArray['background'] = chartArrayBackground;
    chartArray['size'] = chartArraySize;
    chartArray["id"] = name;
    chartArray["title"] = title;
    LoadChart(id, chartArray);
}

function topBillClick() {
    if (getCookie("userID") == "") {
        return false;
    }
    billPage = 0;
    billNowUrl = "./Core/Bill/getBill.php";
    AjaxRequest("./Core/Bill/getUserRegYear.php", function (result) {
        var time = new Date();
        var recTime = time.getFullYear() - Number(result);
        var startYear = time.getFullYear() - recTime;
        var endYear = time.getFullYear();
        var billTime = document.getElementById('billYearTime');
        for (var n = startYear; n <= endYear; n++) {
            billTime.options.add(new Option(n + "年", n));
        }
    });
    AjaxRequest(billNowUrl, function (result) {
        var date = ArrayDate(result);
        //console.log(date);
        var HTMLcode = "";
        var i = 1;
        if (result == "") {
            document.getElementById("billTable").innerHTML = "<br><br><br><div class='fs-2 text-black-50 pt-5 pb-5 text-center position-absolute start-0 end-0' style='top:25px'>未找到信息</div>";
            return false;
        }
        date.forEach(elements => {
            var timeStartEnd = "";
            var value = "支出";
            if (elements["type"] == 1) {
                value = "收入";
            } else if (elements["type"] == 2) {
                value = "贷款";
            } else if (elements["type"] == 3) {
                value = "放贷";
            }
            if (elements["type"] == 2 || elements["type"] == 3) {
                timeStartEnd = elements["startTime"] + ";" + elements["endTime"];
            }
            HTMLcode += "<tr id='list" + i.toString() + "' onclick='ShowMoreBillInfo(" + i.toString() + ", " + elements["type"] + ")'>";
            HTMLcode += "<td id='listMoney" + i.toString() + "'>" + elements["money"] + "</td>";
            HTMLcode += "<td id='listValue" + i.toString() + "' class='display-none-wms text-truncate' style='--displayShow:table-cell'>" + decodeURIComponent(elements["value"]) + "</td>";
            HTMLcode += "<td id='listName" + i.toString() + "'>" + decodeURIComponent(elements["name"]) + "</td>";
            HTMLcode += "<td id='listType" + i.toString() + "'>" + value + "</td>";
            HTMLcode += "<td id='listTime" + i.toString() + "' class='display-none-wms' style='--displayShow:table-cell' name='" + timeStartEnd + "'>" + elements["time"] + "</td>";
            HTMLcode += "</tr>";
            i++;
        });
        document.getElementById("billTable").innerHTML = HTMLcode;
    });
}

function SearchBill() {
    billPage = 0;
    var searchText = document.getElementById("searchText");
    var billYearTime = document.getElementById("billYearTime");
    var billMouthTime = document.getElementById("billMouthTime");
    var billType = document.getElementById("billType");
    var text = encodeURIComponent(searchText.value);
    var year = billYearTime.value;
    var mouth = billMouthTime.value;
    if (year == "-1") {
        year = "";
    }
    if (mouth == "-1") {
        mouth = "";
    } else {
        year += "-";
    }
    var time = encodeURIComponent(year + mouth);
    var type = billType.value;
    billNowUrl = "./Core/Bill/getBill.php?value=" + text + "&time=" + time + "&type=" + type;
    AjaxRequest(billNowUrl, function (result) {
        if (result == "") {
            document.getElementById("billTable").innerHTML = "<br><br><br><div class='fs-2 text-black-50 pt-5 pb-5 text-center position-absolute start-0 end-0' style='top:25px'>未找到信息</div>";
            return false;
        }
        var date = ArrayDate(result);
        var HTMLcode = "";
        var i = 1;
        date.forEach(elements => {
            var timeStartEnd = "";
            var value = "支出";
            if (elements["type"] == 1) {
                value = "收入";
            } else if (elements["type"] == 2) {
                value = "贷款";
            } else if (elements["type"] == 3) {
                value = "放贷";
            }
            if (elements["type"] == 2 || elements["type"] == 3) {
                timeStartEnd = elements["startTime"] + ";" + elements["endTime"];
            }
            HTMLcode += "<tr id='list" + i.toString() + "' onclick='ShowMoreBillInfo(" + i.toString() + ", " + elements["type"] + ")'>";
            HTMLcode += "<td id='listMoney" + i.toString() + "'>" + elements["money"] + "</td>";
            HTMLcode += "<td id='listValue" + i.toString() + "' class='display-none-wms text-truncate' style='--displayShow:table-cell'>" + decodeURIComponent(elements["value"]) + "</td>";
            HTMLcode += "<td id='listName" + i.toString() + "'>" + decodeURIComponent(elements["name"]) + "</td>";
            HTMLcode += "<td id='listType" + i.toString() + "'>" + value + "</td>";
            HTMLcode += "<td id='listTime" + i.toString() + "' class='display-none-wms' style='--displayShow:table-cell' name='" + timeStartEnd + "'>" + elements["time"] + "</td>";
            HTMLcode += "</tr>";
            i++;
        });
        document.getElementById("billTable").innerHTML = HTMLcode;
    });
}

function LoadMoreBill() {
    billPage++;
    var LoadMoreBillUrl = "";
    if (billNowUrl.indexOf("?") == -1) {
        LoadMoreBillUrl = billNowUrl + "?number=" + String(billPage);
    } else {
        LoadMoreBillUrl = billNowUrl + "&number=" + String(billPage);
    }
    AjaxRequest(LoadMoreBillUrl, function (result) {
        if (result == "") {
            billPage--;
            return false;
        }
        var date = ArrayDate(result);
        //console.log(date);
        var HTMLcode = "";
        var i = 1;
        date.forEach(elements => {
            var timeStartEnd = "";
            var value = "支出";
            if (elements["type"] == 1) {
                value = "收入";
            } else if (elements["type"] == 2) {
                value = "贷款";
            } else if (elements["type"] == 3) {
                value = "放贷";
            }
            if (elements["type"] == 2 || elements["type"] == 3) {
                timeStartEnd = elements["startTime"] + ";" + elements["endTime"];
            }
            HTMLcode += "<tr id='list" + i.toString() + "' onclick='ShowMoreBillInfo(" + i.toString() + ", " + elements["type"] + ")'>";
            HTMLcode += "<td id='listMoney" + i.toString() + "'>" + elements["money"] + "</td>";
            HTMLcode += "<td id='listValue" + i.toString() + "' class='display-none-wms text-truncate' style='--displayShow:table-cell'>" + decodeURIComponent(elements["value"]) + "</td>";
            HTMLcode += "<td id='listName" + i.toString() + "'>" + decodeURIComponent(elements["name"]) + "</td>";
            HTMLcode += "<td id='listType" + i.toString() + "'>" + value + "</td>";
            HTMLcode += "<td id='listTime" + i.toString() + "' class='display-none-wms' style='--displayShow:table-cell' name='" + timeStartEnd + "'>" + elements["time"] + "</td>";
            HTMLcode += "</tr>";
            i++;
        });
        document.getElementById("billTable").innerHTML += HTMLcode;
    });
}

function ShowMoreBillInfo(order, type) {
    var windows = document.getElementById("windows");
    var listMoney = document.getElementById("listMoney" + order.toString());
    var listValue = document.getElementById("listValue" + order.toString());
    var listName = document.getElementById("listName" + order.toString());
    var listTime = document.getElementById("listTime" + order.toString());
    windows.style.display = "block";

    var text = "支出";
    if (type == 1) {
        text = "收入";
    } else if (type == 2) {
        text = "贷款";
    } else if (type == 3) {
        text = "放贷";
    }
    var HTMLcode = "<div class='w-100 h-100 p-3 d-flex align-items-center justify-content-sm-center flex-column' style='overflow-y: auto;'>";
    HTMLcode += "<form method=\"post\" action=\"./Core/Bill/del.php\" id=\"billForm\" target=\"formSubmit\" class=\"eject form-control border-0 shadow-lg w-wms-100 d-flex align-items-center flex-column pb-5 m-sm-0 mb-4 mt-4\" style=\"--widthSet:576px\">";
    HTMLcode += "<div class=\"w-100 d-flex justify-content-end mt-2\">";
    HTMLcode += "<button type=\"button\" class=\"btn\" onclick=\"CloseWindow()\"><i class=\"bi bi-x-lg\"></i></i></button>";
    HTMLcode += "</div>";
    HTMLcode += "<h1>账单</h1>";
    HTMLcode += "<div class=\"mb-3 row w-80\">";
    HTMLcode += "<label for=\"billMoney\" class=\"col-sm-3 col-form-label\">金额：</label>";
    HTMLcode += "<div class=\"col-sm-9 d-flex\">";
    HTMLcode += "<input type=\"number\" step = \"0.01\" readonly=\"readonly\" value=\"" + listMoney.innerHTML + "\" class=\"form-control\" id=\"billMoney\">";
    HTMLcode += "<div style=\"width: 10rem;\" class=\"ms-2\">";
    HTMLcode += "<input type=\"text\" readonly=\"readonly\" value=\"" + text + "\" class=\"form-control\" id=\"billType\">";
    HTMLcode += "<input type=\"text\" readonly=\"readonly\" style=\"display:none\" name=\"type\" value=\"" + type.toString() + "\" class=\"form-control\">";
    HTMLcode += "</div>";
    HTMLcode += "</div>";
    HTMLcode += "</div>";
    HTMLcode += "<div class=\"mb-3 row w-80\">";
    HTMLcode += "<label for=\"billMoney\" class=\"col-sm-3 col-form-label\">详细：</label>";
    HTMLcode += "<div class=\"col-sm-9\">";
    HTMLcode += "<textarea class=\"form-control\" id=\"billValue\" readonly=\"readonly\" rows=\"3\">" + listValue.innerHTML + "</textarea>";
    HTMLcode += "</div>";
    HTMLcode += "</div>";
    HTMLcode += "<div class=\"mb-3 row w-80\">";
    HTMLcode += "<label for=\"billName\" class=\"col-sm-3 col-form-label\">类型：</label>";
    HTMLcode += "<div class=\"col-sm-9\">";
    HTMLcode += "<input type=\"text\" class=\"form-control\" readonly=\"readonly\" name=\"name\" value=\"" + listName.innerHTML + "\" id=\"billName\">";
    HTMLcode += "</div>";
    HTMLcode += "</div>";
    if (type == 2 || type == 3) {
        var startEndTime = listTime.getAttribute("name");
        var startTime = startEndTime.substr(0, startEndTime.indexOf(";"));
        var endTime = startEndTime.substr(startEndTime.indexOf(";") + 1);
        HTMLcode += "<div class=\"mb-3 row w-80\">";
        HTMLcode += "<label for=\"billTime\" class=\"col-sm-3 col-form-label\">起始时间：</label>";
        HTMLcode += "<div class=\"col-sm-9\">";
        HTMLcode += "<input type=\"date\" class=\"form-control\" name=\"time\" readonly=\"readonly\" value=\"" + startTime + "\" id=\"billTime\">";
        HTMLcode += "</div>";
        HTMLcode += "</div>";
        HTMLcode += "<div class=\"mb-3 row w-80\">";
        HTMLcode += "<label for=\"billTime\" class=\"col-sm-3 col-form-label\">截止时间：</label>";
        HTMLcode += "<div class=\"col-sm-9\">";
        HTMLcode += "<input type=\"date\" class=\"form-control\" name=\"time\" readonly=\"readonly\" value=\"" + endTime + "\" id=\"billTime\">";
        HTMLcode += "</div>";
        HTMLcode += "</div>";
    }
    HTMLcode += "<div class=\"mb-3 row w-80\">";
    HTMLcode += "<label for=\"billTime\" class=\"col-sm-3 col-form-label\">时间：</label>";
    HTMLcode += "<div class=\"col-sm-9\">";
    HTMLcode += "<input type=\"datetime-local\" class=\"form-control\" name=\"time\" readonly=\"readonly\" value=\"" + listTime.innerHTML + "\" id=\"billTime\">";
    HTMLcode += "</div>";
    HTMLcode += "</div>";
    HTMLcode += "<button onclick=\"DelBillButton(" + order + ")\" class=\"btn btn-outline-dark w-50\" type=\"submit\">删除</button>";
    HTMLcode += "</form>";
    HTMLcode += "</div>";
    windows.innerHTML = HTMLcode;
}

function DelBillButton(order) {
    document.getElementById("list" + order.toString()).remove();
    CloseWindow();
}

function ShowAddTips() {
    var windows = document.getElementById("windows");
    var HTMLcode = "<div class='w-100 h-100 p-3 d-flex align-items-center justify-content-sm-center flex-column'>";
    HTMLcode += "<div class='eject card shadow-lg border-0 h-auto mb-3'>";
    HTMLcode += "<div class='card-body'>";
    HTMLcode += "<div id='addSaveState' name='0'>";
    HTMLcode += "<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>";
    HTMLcode += "保存中...";
    HTMLcode += "</div>";
    HTMLcode += "</div>";
    HTMLcode += "</div>";
    HTMLcode += "</div>";
    windows.innerHTML = HTMLcode;
    windows.style.display = "block";
    ShowAddTipsSetTimeOut = setTimeout(() => {
        var state = document.getElementById("addSaveState");
        if (state.getAttribute("name") == "0") {
            state.innerHTML = '<i class="bi bi-x-circle"></i> 保存失败';
            state.class = "text-danger";
            setTimeout(() => {
                CloseWindow();
            }, 3000);
        }
    }, 5000);
}

function ChageAddState() {
    clearTimeout(ShowAddTipsSetTimeOut);
    var state = document.getElementById("addSaveState");
    state.setAttribute("name", "1");
    state.innerHTML = '<i class="bi bi-check-circle"></i> 保存成功';
    state.class = "text-success";
    setTimeout(() => {
        CloseWindow();
        document.getElementById('billForm').reset();
    }, 3000);
}

function ChageAddError() {
    clearTimeout(ShowAddTipsSetTimeOut);
    var state = document.getElementById("addSaveState");
    state.setAttribute("name", "2");
    state.innerHTML = '<i class="bi bi-exclamation-circle"></i> 错误（数据缺失）';
    state.class = "text-success";
    setTimeout(() => {
        CloseWindow();
    }, 3000);
}