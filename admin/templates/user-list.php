<?php $lists = getFbLoggedinUsers();  ?>
<div class="wrap">
    <h1>Facebook Logged-in Users</h1><hr>
    <table class="widefat">
        <thead>
            <tr>
                <th>Date</th><th>Email</th><th>Name</th><th>Link</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($lists as $l): ?>
            <tr>
                <td><?php echo date('d/m/Y', strtotime($l->created_at)); ?></td>
                <td><?php echo $l->fbemail; ?></td>
                <td><?php echo $l->fullname; ?></td>
                <td><a href="https://facebook.com/profile.php?id=<?php echo $l->fbid; ?>">go to profile</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>