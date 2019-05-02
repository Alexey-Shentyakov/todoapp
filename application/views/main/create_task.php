<div class="row text-center">
<div class="col-12">
<form action="/main/createTask" enctype="multipart/form-data" method="post">
    <fieldset>
        <legend></legend>

        <!-- name -->
        <p>
        <label for="name">Name</label>
        <input type="text" id="name" name="name" size="30" required>
        </p>

        <!-- body -->
        <p>
        <label for="body">Body</label>
        <textarea id="body" name="body" rows="10" cols="30" required></textarea>
        </p>

        <!-- parent -->
        <p><label for="parent_id">Parent</label>
        <select id="parent_id" name="parent_id">
            <option value="">-</option>
        <?php
        foreach ($parents as $p) {
            echo '<option value="' . $p->id . '">' . $p->name . '</option>' . "\n";
        }
        ?>
        </select></p>

        <!-- target time -->
        <p>
        <label for="datetime">Target Time</label>
        <input type="datetime-local" id="datetime" name="target_time" required></p>

        <!-- user -->
        <p>
        <label for="user_id">User ID</label>
        <select id="user_id" name="user_id">
        <?php
        foreach ($users as $u) {
            echo '<option value="' . $u->id . '">' . $u->name . ' (' . $u->email . ')' . '</option>' . "\n";
        }
        ?>
        </select>
        </p>

        <!-- csrf -->
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
    </fieldset>
    <p><input type="submit" value="Submit"></p>
</form>
</div>
</div>

<script>
$(document).ready(
    function(){
        var now = new Date($.now())
        , year
        , month
        , date
        , hours
        , minutes
        , seconds
        , formattedDateTime
        ;
    
      year = now.getFullYear();
      month = now.getMonth().toString().length === 1 ? '0' + (now.getMonth() + 1).toString() : now.getMonth() + 1;
      date = now.getDate().toString().length === 1 ? '0' + (now.getDate()).toString() : now.getDate();
      hours = now.getHours().toString().length === 1 ? '0' + now.getHours().toString() : now.getHours();
      minutes = now.getMinutes().toString().length === 1 ? '0' + now.getMinutes().toString() : now.getMinutes();
      seconds = now.getSeconds().toString().length === 1 ? '0' + now.getSeconds().toString() : now.getSeconds();
    
      formattedDateTime = year + '-' + month + '-' + date + 'T' + hours + ':' + minutes + ':' + seconds;

      $("#datetime").attr("min", formattedDateTime);
      $("#datetime").val(formattedDateTime);

      $("#name").val("new task");
      $("#body").val("task body");
    }
);
</script>
