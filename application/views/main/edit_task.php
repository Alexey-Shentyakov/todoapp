<div class="row text-center">
<div class="col-12">
<form action="/main/editTask" enctype="multipart/form-data" method="post">
    <fieldset>
        <legend></legend>
    <p>
        <input type="datetime-local" id="datetime" name="target_time" required value="<?php echo $task->target_time ?>">
    </p>

    <p>
        <label for="name">Name</label><input type="text" id="name" name="name" size="30" required value="<?php echo $task->name ?>">
    </p>

    <p>
        <label for="body">Body</label><textarea id="body" name="body" rows="10" cols="30" required><?php echo $task->body ?></textarea>
    </p>

    <p>
        <label for="parent_id">Parent</label>
    <select id="parent_id" name="parent_id">
        <option value="">-</option>
    <?php
    foreach ($parents as $p) {
        $option = '<option value=';
        $option .= '"' . $p->id . '"';

        if ($p->id === $task->parent_id) {
            $option .= " selected";
        }

        $option .= '>' . $p->name . '</option>' . "\n";
        echo $option;
    }
    ?>
    </select>
    </p>

    <p>
    <label for="status">Status</label>
    <select id="status" name="status" required>
        <?php
            switch ($task->status) {
                case 'active':
                    echo '<option value="active">Active</option>';
                    break;
                    
                case 'closed':
                    echo '<option value="closed">Closed</option>';
                    break;
            }
        ?>
    </select>
    </p>

    <!-- user -->
    <p>
    <label for="user_id">User ID</label>
    <select id="user_id" name="user_id">
    <?php
    foreach ($users as $u) {
        $option = '<option value="' . $u->id . '"';

        if ($u->id === $task->user_id) {
            $option .= " selected";
        }
        
        $option .= '>' . $u->name . ' (' . $u->email . ')' . '</option>' . "\n";
        echo $option;
    }
    ?>
    </select>
    </p>

    <!-- csrf -->
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

    <!-- task id -->
    <input type="hidden" name="task_id" value="<?php echo $task->id ?>">
    
    </fieldset>
    <p><input type="submit" value="Submit"></p>
</form>
</div>
</div>
