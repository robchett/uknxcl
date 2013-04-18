<h3>New flight with coordinates</h3>
<form name="coord" id="input" target="upload_target" onsubmit="startUpload('WriteHereNewFlight');"
      action="/inc/module/add_flight/coordOnly.php" method="post">
    <table>
        <thead>
        <tr>
            <th width="100"></th>
            <th width="185"></th>
        </tr>
        </thead>
        <tr>
            <td>Pilot:<a title="Choose the pilots name" class="help">[?]</a></td>
            <td><select name="pilot" id='pilot' onchange="ddCheck2(this,0)"
                        class="Pickerfalse" required>
                    <option value="Default">Select a Pilot</option>
                    <?php
                    AlphaPilot($pilot)?>
                </select></td>
        </tr>
        <tr>
            <td>Glider:<a title="Choose the pilots glider" class="help">[?]</a></td>
            <td><select name="glider" id="glider" onchange="ddCheck2(this,1)"
                        class="Pickerfalse" required>
                    <option value="Default">Select a Glider</option>
                    <?php
                    AlphaGlider($glider)?>
                </select></td>
        </tr>
        <tr>
            <td>Club:<a title="Choose the pilots club" class="help">[?]</a></td>
            <td><select name="club" id="club" onchange="ddCheck2(this,2)"
                        class="Pickerfalse" required>
                    <option value="Default">Select a Club</option>
                    <?php
                    AlphaClub($club)?>
                </select></td>
        </tr>
        <tr>
            <td>Date:<a title="Date for the flight in dd/mm/yyyy format." class="help">[?]</a></td>
            <td><input type="date" name="date" id="SelectedDate" class="Chooserfalse" onchange="dateCheck(this.value)"/>
            </td>
        </tr>
    </table>
    <table>
        <thead>
        <tr>
            <th width="100"></th>
            <th width="185"></th>
            <th width="16"></th>
        </tr>
        </thead>

        <tr>
            <td>Type of flight:<a title="you can only enter open distance flights with coordinates" class="help">[?]</a>
            </td>
            <td><select name="type" onchange="getMultiplier2()" class="Picker">
                    <option value="0">Open Distance</option>
                </select></td>
        </tr>
        <tr>
            <td>Pre-declared?<a title="Was the flight declared before hand" class="help">[?]</a></td>
            <td><input type="checkbox" id="defined" name="defined" value="Yes"
                       onchange="getMultiplier2()"/></td>
        </tr>
        <tr>
            <td>Ridge lift?<a title="Was the flight maily ridge lift" class="help">[?]</a></td>
            <td><input type="checkbox" name="ridge" value="Yes"
                       onchange="getMultiplier2()"/></td>
        </tr>
        <tr>
            <td>Launch Type<a title="Method of launch" class="help">[?]</a></td>
            <td><select name="launch" class="Picker">
                    <option value="0">Foot</option>
                    <option value="1">Aerotow</option>
                    <option value="2">Winch</option>
                </select></td>
        </tr>
        <tr>
            <td>Coordinates:<a title="Start and End coordinates of the form (OS GRID REF) XX000000;XX00000"
                               class="help">[?]</a></td>
            <td><input type="text" style="width: 263px"
                       pattern="((H[L-Z]|N[A-HJ-Z]|S[A-HJ-Z]|T[ABFGLMQRVW])[0-9]{6};)+(H[L-Z]|N[A-HJ-Z]|S[A-HJ-Z]|T[ABFGLMQRVW])[0-9]{6}"
                       name="cords" onchange="calculate2()" class="Chooserfalse"
                       title="Enter coordinates of the form XX000000;XX00000. No ending ';'"
                       id="cords"/></td>
        </tr>
        <tr>
            <td>Distance (km):<a title="Calculated automatically, for reference only" class="help">[?]</a></td>
            <td><input type="text" name="dist" style="width: 263px" id="distance2"
                       readonly="readonly" class="Chooserfalse"
                       title="Calculated from the coordinates give, if less than 10 only appears in certain tables."/>
            </td>
        </tr>
        <tr>
            <td>Multiplier:<a title="The multiplier you will recieve" class="help">[?]</a></td>
            <td><input type="text" name="multi" style="width: 263px"
                       readonly="readonly" value="1" class="Chooser"/></td>
            <td><a id="multiplier"></a><br/>
            </td>
        </tr>
        <tr>
            <td>Other info<a title="Info that will be dispayed with the flight" class="help">[?]</a></td>
            <td><textarea cols="31" rows="3" name="vis_info"></textarea></td>
        </tr>
        <tr>
            <td>Admin info<a title="info for admins only" class="help">[?]</a></td>
            <td><textarea cols="31" rows="3" name="invis_info"></textarea></td>
        </tr>
    </table>
    I wish to delay publication until this info has been acted upon. <input
        type="checkbox" name="delay"/><br/>
    This flight does not qualify but I wish it to be in my personal log. <input
        type="checkbox" name="personal"/><br/>
    <a id="agree2" class="red">I agree to publish this flight to the public
        domain </a> <input type="checkbox" name="agree" required="required"
                           onchange="agreeToggle2(this.checked)"/><br/>
    <input type="submit" disabled="disabled" id="submitCoord" name="cordssubmit"/><a onclick="$('#coords').toggle();$('#kml').toggle();">submit
        with igc.</a>
</form>
