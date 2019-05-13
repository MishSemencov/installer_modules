<?php
if(isset($_GET['city']))
{
    file_put_contents(
        $_SERVER['DOCUMENT_ROOT'] . '/local/its.weather/settings.json',
        json_encode(["city" => $_GET['city']])
    );
    echo '<p style="text-align: center;">Успешно!</p>';
    CModule::IncludeModule("itsolutionru.weather");
    AgentClassWeather::GetWeather();
}
?>
<style>
    #form {
        border: 1px solid lightgray;
        padding: 30px 40px;
        border-radius: 15px;
        text-align: center;
    }

    #form input[type="text"] {
        padding: 10px;
        border-radius: 10px;
        font-size: 16px;
        width: 100%;
        border: 1px solid lightgray;
        outline: none;
        box-sizing: border-box;
    }

    #form input[type="submit"] {
        margin-top: 15px;
        padding: 5px 20px;
        border: none;
        font-size: 12px;
        border-radius: 5px;
    }

    #form input[type="submit"]:hover {
        cursor: pointer;
        outline: none;
    }

    .form-tips_wrapper {
        min-width: 400px;
        position: relative;
        box-sizing: border-box;
    }

    .form-tips_items {
        border: 1px solid rgba(0, 0, 0, 0.2);
        border-radius: 5px;
        position: absolute;
        top: 100%;
        width: 100%;
        margin-top: 5px;
        z-index: 100;
        background-color: #fff;
        display: none;
    }

    .form-tips_items p {
        text-align: left;
        padding: 8px 10px;
        margin: 0px;
        transition: all 0s;
    }

    .form-tips_items p:hover {
        transition: all 0.3s;
        cursor: pointer;
        background-color: rgba(0, 0, 0, 0.1);
    }
</style>

<div class="wrapper">
    <form method="GET" id="form" action="settings.php">
        <div class="form-tips_wrapper">
            <input id="search" type="text" autocomplete="off" placeholder="Введите название...">
            <div class="form-tips_items" id="tips">
            </div>
        </div>
        <input id="submit" type="submit" value="Отправить">
        <input type="hidden" id="result" name="city" value="">
        <input type="hidden" id="result" name="lang" value="ru">
        <input type="hidden" id="result" name="mid" value="itsolutionru.weather">
        <input type="hidden" id="result" name="mid_menu" value="1">
    </form>
</div>

<script>
    let search,
        submit,
        tipsArray = [];
    result,
        tips

    tips = document.getElementById("tips");
    search = document.getElementById("search");
    submit = document.getElementById("submit");
    result = document.getElementById("result");

    function addTip(name, url) {
        let tip = document.createElement(`p`);
        tip.innerHTML = name;
        tips.appendChild(tip);
        tip.addEventListener("click", () => {
            result.setAttribute("value", url);
            search.value = name;
            tips.style.display = "none";
        })
    }

    search.addEventListener("keyup", (e) => {
        // search.setAttribute("disabled", "disable");
        fetch(`https://betify.pro/app/itsolutionru.weather/?query=${e.target.value}`)
            .then(response => response.json())
            .then(data => {
                // console.log(data);
                tipsArray = [];
                for (let i = 0; i < data.length; i++) {
                    if (data[i].type === "city") {
                        tipsArray.push({
                            name: data[i].name,
                            url: data[i].url
                        })
                    }
                }

                tips.innerHTML = "";
                if (tipsArray.length === 0) tips.style.display = "none";
                else {
                    for (let i = 0; i < tipsArray.length; i++) {
                        addTip(tipsArray[i].name, tipsArray[i].url)
                    }
                    tips.style.display = "block"
                }

                // console.log(tipsArray);
            })
            .catch(error => {
                return 0;
            })
    })

</script>
