function createMonthCalendarObj(year, month) {
    // カウントアップする日付
    let date = 1;

    const begin = new Date(year, month, 1);
    const beginYear = begin.getFullYear();
    const beginMonth = begin.getMonth();
    const beginDay = begin.getDay();
    const end = new Date(year, month + 1, 0); // 当月末日
    const endDate = end.getDate();
    const endDay = end.getDay();

    // 月
    const calendar = [];
    for (let row = 0; row < 6; row++) {
        if (date > endDate) {
            continue;
        }

        // 週
        const week = [];

        for (let col = 0; col < 7; col++) {
            if (row === 0 && col < beginDay) {
                const beforeDate = new Date(
                    beginYear,
                    beginMonth,
                    -(beginDay - 1 - col)
                ).getDate();
                week.push(beforeDate);
            } else if (date > endDate) {
                const afterDate = new Date(
                    beginYear,
                    beginMonth,
                    endDate + col - endDay
                ).getDate();
                week.push(afterDate);
            } else {
                week.push(date);
                date++;
            }
        }
        calendar.push(week);
    }

    const monthCalendar = {
        year: beginYear,
        month: beginMonth,
        calendar: calendar,
    };

    return monthCalendar;
}

function drawMonthCalendar(monthCalendarObj, targetElem) {
    const year = monthCalendarObj.year;
    const month = monthCalendarObj.month;
    const calendar = monthCalendarObj.calendar;

    const table = $(`
    <table class="table table-bordered table-sm table-calendar">
        <caption></caption>
        <thead>
            <tr>
            <th class="sun">S</th><th class="mon">M</th><th class="tue">T</th><th class="wed">W</th><th class="thu">T</th><th class="fri">F</th><th class="sat">S</th>
            </tr>
        </thead>
        <tbody></tbody>
        </table>
    `);

    // const japaneseYear = new Date(
    //     year,
    //     month
    // ).toLocaleDateString('ja-JP-u-ca-japanese', {
    //     era: 'long',
    //     year: 'numeric',
    // });
    $(table)
        .children("caption")
        .html(
            '<a href="' +
                location.href.split("/music/")[0] +
                "/music/search?created_at=" +
                year +
                "-" +
                `${`0${month + 1}`.slice(-2)}` +
                '">' +
                `${year}年 ${`0${month + 1}`.slice(-2)}月` +
                "</a>"
        );

    calendar.forEach((week, rowIndex) => {
        const row = $("<tr>");
        week.forEach((day, colIndex) => {
            const col = $("<td>").html(
                '<a href="' +
                    location.href.split("/music/")[0] +
                    "/music/search?created_at=-" +
                    `${`0${month + 1}`.slice(-2)}` +
                    "-" +
                    `${`0${day}`.slice(-2)}` +
                    '">' +
                    day +
                    "</a>"
            );
            col.addClass(
                ["sun", "mon", "tue", "wed", "thu", "fri", "sat"][colIndex]
            );

            const isBeforeDate = rowIndex === 0 && day > 7;
            const isAfterDate = rowIndex > 1 && day < 7;
            if (isBeforeDate || isAfterDate) {
                col.addClass("out-date");
            }

            if (
                year == new Date().getFullYear() &&
                month == new Date().getMonth() &&
                day == new Date().getDate()
            ) {
                col.addClass("today");
            }

            row.append(col);
        });
        $(table).children("tbody").append(row);
    });

    $(targetElem).append(table);
}

document.addEventListener(
    "DOMContentLoaded",
    () => {
        const now = new Date();

        // const calendarBefore = createMonthCalendarObj(now.getFullYear(), now.getMonth() - 1);
        // drawMonthCalendar(calendarBefore, '#month-calendar-before');

        const calendarCurrent = createMonthCalendarObj(
            now.getFullYear(),
            now.getMonth()
        );
        drawMonthCalendar(calendarCurrent, "#month-calendar-current");

        // const calendarAfter = createMonthCalendarObj(now.getFullYear(), now.getMonth() + 1);
        // drawMonthCalendar(calendarAfter, '#month-calendar-after');
    },
    false
);
