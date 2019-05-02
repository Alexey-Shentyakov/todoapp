<div class="row">
<div class="col-12">
<table class="table" id="tasks_table">
<thead>
<tr>
    <th>id</th>
    <th>target_time</th>
    <th>name</th>
    <th>body</th>
    <th>parent</th>
    <th>status</th>
    <th>user</th>
    <th>edit</th>
    <th>close</th>
</tr>
</thead>
<tbody>

</tbody>
</table>
</div>
</div>

<nav aria-label="Page navigation">
  <ul class="pagination">


  </ul>
</nav>

<script>

$(document).ready(
function() {
    get_page(<?php echo $page ?>);
}
);

function get_page(page) {
$.ajax({
    method: "GET",
    dataType: "json",
    url: "/main/listTasksAjax/" + page,
    success: function(data) {
        //console.log(data);

        var table_body = "";
        var table_item = "";
        
        data.tasks.forEach(function callback(currentValue, index, array) {
            table_item = "<tr>";
            table_item += "<td>" + currentValue.id + "</td>";
            table_item += "<td>" + currentValue.target_time + "</td>";
            table_item += "<td>" + currentValue.name + "</td>";
            table_item += "<td>" + currentValue.body + "</td>";
            table_item += "<td>" + currentValue.parent_name + "</td>";
            table_item += "<td>" + currentValue.status + "</td>";
            table_item += "<td>" + currentValue.user_name + "</td>";
            table_item += "<td><a href=\"/main/editTask/" + currentValue.id + "\">EDIT</a></td>";
            table_item += "<td><a href=\"/main/closeTask/" + currentValue.id + "\">CLOSE</a></td>";
            table_item += "</tr>";

            table_body += table_item;
        });

        $("#tasks_table.table tbody").html(table_body);

        var page_nav = "";

        // page navigation

        if (page-1 >= 1) {
            page_nav += '<li class="page-item"><a class="page-link" href="#" id="prev_page_link">Previous</a></li>';
        }

        page_nav += '<li class="page-item"><a class="page-link" href="#" id="curr_page_link">' + page + '</a></li>';

        if (page+1 <= data.pages_num) {
            page_nav += '<li class="page-item"><a class="page-link" href="#" id="next_page_link">Next</a></li>';
        }

        $("html body div.container nav ul.pagination").html(page_nav);

        $("#prev_page_link").click(
            function (event) {
                event.preventDefault();
                get_page(page-1);
            }
        );

        $("#curr_page_link").click(
            function (event) {
                event.preventDefault();
                get_page(page);
            }
        );

        $("#next_page_link").click(
            function (event) {
                event.preventDefault();
                get_page(page+1);
            }
        );
    }
});
}
</script>
