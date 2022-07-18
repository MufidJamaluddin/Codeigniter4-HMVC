<section>

	<h1>Dashboard Page</h1>

    <table border="1px">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data ?? [] as $key => $itemUser):?>
            <tr>
                <td><?=$itemUser->getId() ?? '' ?></td>
                <td><?=$itemUser->getName() ?? '' ?></td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>

	<p>If you would like to edit this page you will find it located at:</p>

	<pre><code>app/Views/admin/dashboard.php</code></pre>

	<p>The corresponding controller for this page can be found at:</p>

	<pre><code>app/Modules/Admin/Controllers/Dashboard.php</code></pre>

</section>