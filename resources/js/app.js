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
            // START TABLE LEFT
            $.each(data.left_companies, (key, left_company) => {
                tableDataLeft += `<tr>`;
                // Tentukan Class
                let classLeft = "empty";
                if (left_company.is_company) {
                    classLeft = "company";
                }
                if (left_company.is_home){
                    classLeft = "home";
                }

                if(left_company.num_occupant > 0){
                    tableDataLeft += `
                    <td class='${classLeft}' id='${left_company.id}'>
                        <div class="dot">${left_company.teams[0].id}</div>
                    </td>`;
                }
                else{
                    tableDataLeft += `
                    <td class='${classLeft}' id='${left_company.id}'></td>
                    `;
                }
                tableDataLeft += `</tr>`;
            });
            $("#mainTableLeft").html(tableDataLeft);
            // END TABLE LEFT

            // START TABLE UPPER
            tableDataUpper += `<tr>`;
            $.each(data.upper_companies, (key, upper_company) => {
                let classUpper = "empty";
                if (upper_company.is_company) {
                    classUpper = "company";
                }
                if (upper_company.is_home) {
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

                if (territory.is_wall) classes = "wall";
                else if (territory.is_water) classes = "water";
                else if (territory.is_harbour) {
                    classes = "harbour";
                    onclick = true;
                    alias = "P" + index_pelabuhan;
                    index_pelabuhan++;
                } else if (territory.is_company) classes = "company";
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
                    tableData += `<td class='${classes}' id='${territory.id}'>`;
                    if (territory.num_occupant > 0) {
                        tableData += `<div class='dot'> ${territory.teams[0].id} </div>`;
                        alias = "";
                    }
                    tableData += ` ${alias} `;
                    tableData += `</td>`;
                }

                // Nutup TR
                if (territory.close_tr) tableData += `</tr>`;
            }),
            $("#mainTable").html(tableData);
            // END TABLE MAIN

            // START TABLE LOWER
            tableDataLower += `<tr>`;
            $.each(data.lower_companies, (key, lower_company) => {
                let classLower = "empty";
                if (lower_company.is_company) {
                    classLower = "company";
                }
                if (lower_company.is_home) {
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
            $("#mainTableLower").html(tableDataLower);
            // END TABLE LOWER

            // START TABLE RIGHT
            $.each(data.right_companies, (key, right_company) => {
                tableDataRight += `<tr>`;
                // Tentukan Class
                let classRight = "empty";
                if (right_company.is_company) {
                    classRight = "company";
                }
                if (right_company.is_home) {
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
            $("#mainTableRight").html(tableDataRight);
            // END TABLE RIGHT
            
            $(".btn-control-action").attr("disabled", false);
        },
    });
});
