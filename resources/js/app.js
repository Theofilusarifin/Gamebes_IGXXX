require("./bootstrap");

window.Echo.channel("update-map").listen(".UpdateMapMessage", (e) => {
    console.log(e.message);
    let tableData = "";
    $.ajax({
        type: "POST",
        url: "/map/update-map",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            let column = 44;
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
            $(".btn-control-action").attr("disabled", false);
        },
    });
});
