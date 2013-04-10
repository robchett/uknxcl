<div id="admin_box">
    <h2>Admin Options</h2>

    There are parts of this site which we are limiting access to currently these include access to the airspace overlay
    for flights which have been submitted as well as access to the glider database to edit glider info:

    <div>
        <h3>Airspace access</h3>

        <form name="airspace_access" target="upload_target" method="post"
              action="/inc/module/admin/view/airspace_access.php" onsubmit="startUpload('AdminWriteHere');">
            Password:<input type="password" name="password"/><br/>
            <input type="submit"/>
        </form>

    </div>
    <div>

        <h3>Gilder Database access</h3>

        <form name="database_access" target="upload_target" method="post"
              action="/inc/module/admin/view/database_access.php" onsubmit="startUpload('AdminWriteHere');">
            Password:<input type="password" name="password" required/><br/>
            <input type="submit"/>
        </form>

    </div>
    <div>
        <h3>Turnpoint radius guide</h3>

        <form name="database_access" target="upload_target" method="post"
              action="/inc/module/admin/view/radius.php"
              onsubmit="startUpload('AdminWriteHere');">
            Password:<input type="password" name="password" required/><br/>
            <input type="submit"/>
        </form>

    </div>
    <div>
        <h3>Add news article</h3>

        <form name="database_access" target="upload_target" method="post"
              action="/inc/module/admin/view/news.php"
              onsubmit="startUpload('AdminWriteHere');">
            Password:<input type="password" name="password" required/><br/>
            <input type="submit"/>
        </form>
    </div>
</div>