require("./bootstrap");

window.Echo.channel("update-map").listen(".UpdateMapMessage", (e) => {
    let tableDataLeft = "";
    let tableDataUpper = "";
    let tableData = "";
    let tableDataLower = "";
    let tableDataRight = "";

    $.ajax({
        type: "POST",
        url: "/map/update-map",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            console.log(data);
            // START TABLE LEFT
            $.each(data.left_companies, (key, left_company) => {
                tableDataLeft += `<tr>`;
                // Tentukan Class
                let classLeft = "empty";
                if (left_company.is_company == 1) {
                    classLeft = "company";
                }
                if (left_company.is_home == 1) {
                    classLeft = "home";
                }

                if (left_company.num_occupant > 0) {
                    tableDataLeft += `
                    <td class='${classLeft}' id='${left_company.id}'>
                        <div class="dot">${left_company.teams[0].id}</div>
                    </td>`;
                } else {
                    tableDataLeft += `
                    <td class='${classLeft}' id='${left_company.id}'></td>`;
                }
                tableDataLeft += `</tr>`;
            });
            console.log(tableDataLeft);

            $("#mainTableLeft").html(tableDataLeft);
            // END TABLE LEFT

            // START TABLE UPPER
            tableDataUpper += `<tr>`;
            $.each(data.upper_companies, (key, upper_company) => {
                let classUpper = "empty";
                if (upper_company.is_company == 1) {
                    classUpper = "company";
                }
                if (upper_company.is_home == 1) {
                    classUpper = "home";
                }

                if (upper_company.num_occupant > 0) {
                    tableDataUpper += `<td class='${classUpper}' id='${upper_company.id}'>
                        <div class="dot">${upper_company.teams[0].id}</div>
                    </td>`;
                } else {
                    tableDataUpper += `<td class='${classUpper}' id='${upper_company.id}'></td>`;
                }
            });
            tableDataUpper += `</tr>`;
            console.log(tableDataUpper);

            $("#mainTableUpper").html(tableDataUpper);
            // END TABLE UPPER

            // START TABLE MAIN
            let column = 42;
            let index_pelabuhan = 1;
            let dibuka = "";

            $.each(data.territories, (key, territory) => {
                // Buka TR
                if (key == 0 || key % column == 0) {
                    dibuka = key;
                    tableData += `<tr>`;
                }

                // Inisiasi Class
                let alias = "";
                let classes = "";
                let onclick = false;

                if (territory.is_wall == 1) {
                    classes = "wall";
                } else if (territory.is_water == 1) {
                    classes = "water";
                } else if (territory.is_harbour == 1) {
                    classes = "harbour";
                    onclick = true;
                    alias = "P" + index_pelabuhan;
                    index_pelabuhan++;
                } else if (territory.is_company == 1) {
                    classes = "company";
                }
                // Store
                else if (territory.transport_store_id != null) {
                    classes = "transport_store";
                    alias = territory.transport_store_id;
                } else if (territory.ingridient_store_id != null) {
                    classes = "ingridient_store";
                    alias = territory.ingridient_store_id;
                } else if (territory.machine_store_id != null) {
                    classes = "machine_store";
                    alias = territory.machine_store_id;
                } else if (territory.service_id != null) {
                    classes = "service";
                    alias = territory.service_id;
                }

                // Buat TD
                if (onclick) {
                    tableData += `<td class='${classes}' id='${territory.id}' onclick="setSpawnPoint(${territory.id})">`;
                    if (territory.num_occupant > 0) {
                        tableData += `<div class='dot'> ${territory.teams[0].id} </div>`;
                        alias = "";
                    }
                    tableData += ` ${alias} `;
                    tableData += `</td>`;
                } else {
                    tableData += `<td class='${classes}' num_occupants='${territory.num_occupant}' id='${territory.id}'>`;
                    if (territory.num_occupant == 1) {
                        alias = "";
                        tableData += `<div class='dot'> ${territory.teams[0].id} </div>`;
                    } else if (territory.num_occupant == 2) {
                        alert("masuk num 2");
                        alias = "";
                        tableData += ` 
                        <div class="position_dot_1">
                            <div class="dot_1">
                                ${territory.teams[0].id}
                            </div>
                        </div>
                        <div class="position_dot_2">
                            <div class="dot_2">
                                ${territory.teams[1].id}
                            </div>
                        </div>
                        `;
                    }
                    tableData += ` ${alias} `;
                    tableData += `</td>`;
                }

                // Nutup TR
                if (key == dibuka + column) {
                    tableData += `</tr>`;
                }
            });
            console.log(tableData);

            $("#mainTable").html(tableData);
            // END TABLE MAIN

            // START TABLE LOWER
            tableDataLower += `<tr>`;
            $.each(data.lower_companies, (key, lower_company) => {
                let classLower = "empty";
                if (lower_company.is_company == 1) {
                    classLower = "company";
                }
                if (lower_company.is_home == 1) {
                    classLower = "home";
                }

                if (lower_company.num_occupant > 0) {
                    tableDataLower += `<td class='${classLower}' id='${lower_company.id}'>
                        <div class="dot">${lower_company.teams[0].id}</div>
                    </td>`;
                } else {
                    tableDataLower += `<td class='${classLower}' id='${lower_company.id}'></td>`;
                }
            });
            tableDataLower += `</tr>`;
            console.log(tableDataLower);

            $("#mainTableLower").html(tableDataLower);
            // END TABLE LOWER

            // START TABLE RIGHT
            $.each(data.right_companies, (key, right_company) => {
                tableDataRight += `<tr>`;
                // Tentukan Class
                let classRight = "empty";
                if (right_company.is_company == 1) {
                    classRight = "company";
                }
                if (right_company.is_home == 1) {
                    classRight = "home";
                }

                if (right_company.num_occupant > 0) {
                    tableDataRight += `
                    <td class='${classRight}' id='${right_company.id}'>
                        <div class="dot">${right_company.teams[0].id}</div>
                    </td>`;
                } else {
                    tableDataRight += `
                    <td class='${classRight}' id='${right_company.id}'></td>
                    `;
                }
                tableDataRight += `</tr>`;
            });
            console.log(tableDataRight);

            $("#mainTableRight").html(tableDataRight);
            // END TABLE RIGHT

            $(".btn-control-action").attr("disabled", false);
        },
    });
});
