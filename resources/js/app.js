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
            $.each(data.territories, (key, territory) => {
                let alias = "";

                if(territory.open_tr){
                    tableData += `<tr>`;
                }

                let classes="";
                let onclick=false;

                if (territory.is_wall) classes="wall";
                else if (territory.is_water) classes="water";
                else if (territory.is_harbour){
                    classes="harbour";
                    onclick=true;
                }
                else if (territory.is_company) classes="company";
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

                if(territory.id == 170) alias = "P1";
                else if(territory.id == 221) alias = "P2";
                else if(territory.id == 272) alias = "P3";
                else if(territory.id == 322) alias = "P4";
                else if(territory.id == 372) alias = "P5";
                else if(territory.id == 423) alias = "P6";
                else if(territory.id == 474) alias = "P7";
                else if(territory.id == 524) alias = "P8";
                else if(territory.id == 574) alias = "P9";
                else if(territory.id == 625) alias = "P10";
                else if(territory.id == 676) alias = "P11";
                else if(territory.id == 726) alias = "P12";
                else if(territory.id == 776) alias = "P13";
                else if(territory.id == 827) alias = "P14";
                else if(territory.id == 878) alias = "P15";
                else if(territory.id == 928) alias = "P16";
                else if(territory.id == 978) alias = "P17";
                else if(territory.id == 1029) alias = "P18";
                else if(territory.id == 209) alias = "P19";
                else if(territory.id == 260) alias = "P20";
                else if(territory.id == 311) alias = "P21";
                else if(territory.id == 361) alias = "P22";
                else if(territory.id == 411) alias = "P23";
                else if(territory.id == 462) alias = "P24";
                else if(territory.id == 513) alias = "P25";
                else if(territory.id == 563) alias = "P26";
                else if(territory.id == 613) alias = "P27";
                else if(territory.id == 664) alias = "P28";
                else if(territory.id == 715) alias = "P29";
                else if(territory.id == 765) alias = "P30";
                else if(territory.id == 815) alias = "P31";
                else if(territory.id == 866) alias = "P32";
                else if(territory.id == 917) alias = "P33";
                else if(territory.id == 967) alias = "P34";
                else if(territory.id == 1017) alias = "P35";
                else if(territory.id == 1068) alias = "P36";

                // Buat TD
                if(onclick){
                    tableData += `<td class='${classes}' id='${territory.id}' rowspan='${territory.rowspan}' colspan='${territory.colspan }' onclick="setSpawnPoint(${territory.id})">`;
                        if(territory.num_occupant > 0){
                            tableData += `<div class='dot'> ${territory.teams[0].id} </div>`;
                            alias = "";
                        }
                    tableData += ` ${alias} `;
                    tableData += `</td>`;
                }
                else{
                    tableData += `<td class='${classes}' id='${territory.id}' rowspan='${territory.rowspan}' colspan='${territory.colspan }'>`;
                        if(territory.num_occupant > 0){
                            tableData += `<div class='dot'> ${territory.teams[0].id} </div>`
                            alias = "";
                        }
                    tableData += ` ${alias} `;
                    tableData += `</td>`;
                }

                // Nutup TR
                if(territory.close_tr) tableData += `</tr>`;                    
            }),
            $("#mainTable").html(tableData);
            $(".btn-control-action").attr("disabled", false);
        }
    });
});
