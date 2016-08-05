<?php

include_once('includes/config.inc.php');
include_once('includes/session.inc.php');
include_once('includes/misc.inc.php');

global $errormsg, $blocklogin;

if (isset($_GET['logout']) or isset($_POST['logout'])) {
    logout();
    header("Location: index.php");
    exit(0);
}

if (!is_logged_in() and isset($_POST['formname']) and $_POST['formname'] === "loginform") {
    if (!try_login()) {
        $errormsg = "Error while trying to authenticate you\n";
    }
}

if (is_logged_in() and isset($_POST['formname']) and $_POST['formname'] === "changepwform") {
    if (get_sess_user() == $_POST['username']) {
        if (!update_user(get_sess_user(), is_adminuser(), $_POST['password'])) {
            $errormsg = "Unable to update password!\n";
        }
    } else {
        $errormsg = "You can only update your own password!".$_POST['username'];
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>NSEdit!</title>
    <link href="jquery-ui/themes/base/jquery.ui.all.css" rel="stylesheet" type="text/css"/>
    <link href="jtable/lib/themes/metro/blue/jtable.min.css" rel="stylesheet" type="text/css"/>
    <link href="css/base.css" rel="stylesheet" type="text/css"/>
    <?php if ($menutype === 'horizontal') { ?>
    <link href="css/horizontal-menu.css" rel="stylesheet" type="text/css"/>
    <?php } ?>
    <script src="jquery-ui/jquery-1.10.2.js" type="text/javascript"></script>
    <script src="jquery-ui/ui/jquery.ui.core.js" type="text/javascript"></script>
    <script src="jquery-ui/ui/jquery.ui.widget.js" type="text/javascript"></script>
    <script src="jquery-ui/ui/jquery.ui.mouse.js" type="text/javascript"></script>
    <script src="jquery-ui/ui/jquery.ui.draggable.js" type="text/javascript"></script>
    <script src="jquery-ui/ui/jquery.ui.position.js" type="text/javascript"></script>
    <script src="jquery-ui/ui/jquery.ui.button.js" type="text/javascript"></script>
    <script src="jquery-ui/ui/jquery.ui.resizable.js" type="text/javascript"></script>
    <script src="jquery-ui/ui/jquery.ui.dialog.js" type="text/javascript"></script>
    <script src="jtable/lib/jquery.jtable.min.js" type="text/javascript"></script>
    <script src="js/addclear/addclear.js" type="text/javascript"></script>
</head>

<?php
if (!is_logged_in()) {
?>
<body onload="document.getElementById('username').focus()">
<div class="loginblock">
    <div class="logo">
        <img src="<?php echo $logo ?>" alt="Logo"/>
    </div>
    <div class="login">
        <?php if (isset($errormsg)) {
            echo '<span style="color: red">' . $errormsg . '</span><br />';
        }
        ?>
        <form action="index.php" method="post">
            <table>
                <tr>
                    <td class="label">Username:</td>
                    <td><input id="username" type="text" name="username"></td>
                </tr>
                <tr>
                    <td class="label">Password:</td>
                    <td><input type="password" name="password"></td>
                </tr>
                <?php
                if (isset($secret) && $secret) {
                ?>
                <tr>
                    <td class="label">Remember me:</td>
                    <td><input type="checkbox" name="autologin" value="1"></td>
                </tr>
                <?php
                }
                ?>
                <tr>
                    <td></td>
                    <td><input type="submit" name="submit" value="Log me in!" <?php if ($blocklogin === TRUE) { echo "disabled"; }; ?>></td>
                </tr>
            </table>
            <input type="hidden" name="formname" value="loginform">
        </form>
    </div>
</div>
</body>
</html>

<?php
exit(0);
}

if ($blocklogin === TRUE) {

       echo "<h2>There is an error in your config!</h2>";
       echo "<a href=\"index.php\">Refresh</a>";
       exit(0);
}

?>
<body>
<div id="wrap">
    <div id="dnssecinfo">
    </div>
    <div id="menu" class="jtable-main-container <?php if ($menutype === 'horizontal') { ?>horizontal<?php } ?>">
        <div class="jtable-title menu-title">
            <div class="jtable-title-text">
                NSEdit!
            </div>
        </div>
        <ul>
            <li><a href="#" id="zoneadmin">Zones</a></li>
            <?php if (is_adminuser()) { ?>
                <li><a href="#" id="useradmin">Users</a></li>
            <?php } ?>
            <li><a href="#" id="aboutme">About me</a></li>
            <li><a href="index.php?logout=1">Logout</a></li>
        </ul>
    </div>
    <?php if (isset($errormsg)) {
        echo '<span style="color: red">' . $errormsg . '</span><br />';
    }
    ?>
    <div id="zones">
        <?php if (is_adminuser() or $allowzoneadd === TRUE) { ?>
        <div style="visibility: hidden;" id="ImportZone"></div>
        <div style="visibility: hidden;" id="CloneZone"></div>
        <?php } ?>
        <div class="tables" id="MasterZones">
            <div class="searchbar" id="searchbar">
                <input type="text" id="domsearch" name="domsearch" placeholder="Search...."/>
            </div>
        </div>
        <div class="tables" id="SlaveZones"></div>
    </div>
    <?php if (is_adminuser()) { ?>
    <div id="users">
        <div class="tables" id="Users"></div>
    </div>
    <?php } ?>

    <div id="AboutMe">
        <div class="tables">
            <p>Hi <?php echo get_sess_user(); ?>. You can change your password here.</p>

            <form action="index.php" method="POST">
                <table>
                    <tr>
                        <td class="label">Username:</td>
                        <td><input readonly value="<?php echo get_sess_user(); ?>" id="username" type="text" name="username"></td>
                    </tr>
                    <tr>
                        <td class="label">Password:</td>
                        <td><input type="password" name="password" id="changepw1"></td>
                    </tr>
                    <tr>
                        <td class="label">Password again:</td>
                        <td><input type="password" name="password2" id="changepw2"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" name="submit" id="changepwsubmit" value="Change password!"></td>
                    </tr>
                </table>
                <input type="hidden" name="formname" value="changepwform">
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
window.csrf_token = '<?php echo CSRF_TOKEN ?>';

$(document).ready(function () {
    function csrfSafeMethod(method) {
        // these HTTP methods do not require CSRF protection
        return (/^(GET|HEAD|OPTIONS|TRACE)$/.test(method));
    }
    $.ajaxSetup({
        beforeSend: function(xhr, settings) {
            if (!csrfSafeMethod(settings.type) && !this.crossDomain) {
                xhr.setRequestHeader("X-CSRF-Token", window.csrf_token);
            }
        }
    });
});

function displayDnssecIcon(zone) {
    if (zone.record.dnssec == true) {
        var $img = $('<img class="clickme" src="img/lock.png" title="DNSSec Info" />');
        $img.click(function () {
            $("#dnssecinfo").html("");
            $.each(zone.record.keyinfo, function ( i, val) {
                if (val.dstxt) {
                    $("#dnssecinfo").append("<p><h2>"+val.keytype+"</h2><pre>"+val.dstxt+"</pre></p>");
                }
            });
            $("#dnssecinfo").dialog({
                modal: true,
                title: "DS-records for "+zone.record.name,
                width: 'auto',
                buttons: {
                    Ok: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
        });
        return $img;
    } else {
        return '<img class="list" src="img/lock_open.png" title="DNSSec Disabled" />';
    }
}

function displayExportIcon(zone) {
    var $img = $('<img class="list clickme" src="img/export.png" title="Export zone" />');
    $img.click(function () {
        var $zexport = $.getJSON("zones.php?zoneid="+zone.record.id+"&action=export", function(data) {
            blob = new Blob([data.Record.zone], { type: 'text/plain' });
            var dl = document.createElement('a');
            dl.addEventListener('click', function(ev) {
                dl.href = URL.createObjectURL(blob);
                dl.download = zone.record.name+'.txt';
            }, false);

            if (document.createEvent) {
                var event = document.createEvent("MouseEvents");
                event.initEvent("click", true, true);
                dl.dispatchEvent(event);
            }
        });
    });
    return $img;
}

function displayContent(fieldName, zone) {
    return function(data) {
        if (typeof(zone) != 'undefined') {
            var rexp = new RegExp("(.*)"+zone);
            var label = rexp.exec(data.record[fieldName]);
            var lspan = $('<span>').text(label[1]);
            var zspan = $('<span class="lightgrey">').text(zone);
            return lspan.add(zspan);
        } else {
            var text = data.record[fieldName];
            if (typeof data.record[fieldName] == 'boolean') {
                text == false ? text = 'No' : text = 'Yes';
            }
            return $('<span>').text(text);
        }
    }
}

function getEpoch() {
    return Math.round(+new Date()/1000);
}

$(document).ready(function () {
    var $epoch = getEpoch();

    $('#SlaveZones').jtable({
        title: 'Slave Zones',
        paging: true,
        pageSize: 20,
        sorting: false,
        messages: {
            addNewRecord: 'Add new slave zone',
            editRecord: 'Edit slave zone',
            noDataAvailable: 'No slave zones found',
            deleteConfirmation: 'This slave zone will be deleted. Are you sure?'
        },
        openChildAsAccordion: true,
        actions: {
            listAction: 'zones.php?action=listslaves',
            updateAction: 'zones.php?action=update',
            <?php if (is_adminuser() or $allowzoneadd === TRUE) { ?>
            createAction: 'zones.php?action=create',
            deleteAction: 'zones.php?action=delete',
            <?php } ?>
        },
        fields: {
            id: {
                key: true,
                type: 'hidden'
            },
            name: {
                title: 'Domain',
                width: '8%',
                display: displayContent('name'),
                edit: false,
                inputClass: 'domain',
                listClass: 'domain'
            },
            dnssec: {
                title: 'DNSSEC',
                width: '3%',
                create: false,
                edit: false,
                display: displayDnssecIcon,
                listClass: 'dnssec'
            },
            <?php if (is_adminuser()) { ?>
            account: {
                title: 'Account',
                width: '8%',
                display: displayContent('account'),
                options: function(data) {
                    return 'users.php?action=listoptions&e='+$epoch;
                },
                defaultValue: 'admin',
                inputClass: 'account',
                listClass: 'account'
            },
            <?php } ?>
            kind: {
                create: true,
                type: 'hidden',
                list: false,
                defaultValue: 'Slave'
            },
            masters: {
                title: 'Masters',
                width: '20%',
                display: function(data) {
                    return $('<span>').text(data.record.masters.join('\n'));
                },
                input: function(data) {
                    var elem = $('<input type="text" name="masters">');
                    if (data && data.record) {
                        elem.attr('value', data.record.masters.join(','));
                    }
                    return elem;
                },
                inputClass: 'masters',
                listClass: 'masters'
            },
            serial: {
                title: 'Serial',
                width: '10%',
                display: displayContent('serial'),
                create: false,
                edit: false,
                inputClass: 'serial',
                listClass: 'serial'
            },
            records: {
                width: '5%',
                title: 'Records',
                paging: true,
                pageSize: 20,
                edit: false,
                create: false,
                display: function (zone) {
                    var $img = $('<img class="list" src="img/list.png" title="Records" />');
                    $img.click(function () {
                        $('#SlaveZones').jtable('openChildTable',
                            $img.closest('tr'), {
                                title: 'Records in ' + zone.record.name,
                                openChildAsAccordion: true,
                                actions: {
                                    listAction: 'zones.php?action=listrecords&zoneid=' + zone.record.id
                                },
                                fields: {
                                    name: {
                                        title: 'Label',
                                        width: '7%',
                                        display: displayContent('name'),
                                        listClass: 'name'
                                    },
                                    type: {
                                        title: 'Type',
                                        width: '2%',
                                        display: displayContent('type'),
                                        listClass: 'type'
                                    },
                                    content: {
                                        title: 'Content',
                                        width: '30%',
                                        display: displayContent('content'),
                                        listClass: 'content'
                                    },
                                    ttl: {
                                        title: 'TTL',
                                        width: '2%',
                                        display: displayContent('ttl'),
                                        listClass: 'ttl'
                                    },
                                    disabled: {
                                        title: 'Disabled',
                                        width: '2%',
                                        display: displayContent('disabled'),
                                        listClass: 'disabled'
                                    }
                                }
                            }, function (data) {
                                data.childTable.jtable('load');
                            })
                    });
                    return $img;
                }
            },
            exportzone: {
                title: '',
                width: '1%',
                create: false,
                edit: false,
                display: displayExportIcon,
                listClass: 'exportzone'
            }
        }
    });
    $('#MasterZones').jtable({
        title: 'Master/Native Zones',
        paging: true,
        pageSize: 20,
        messages: {
            addNewRecord: 'Add new zone',
            editRecord: 'Edit zone',
            noDataAvailable: 'No zones found',
            deleteConfirmation: 'This zone will be deleted. Are you sure?'
        },
        toolbar: {
            hoverAnimation: true,
            hoverAnimationDuration: 60,
            hoverAnimationEasing: undefined,
            items: [
                <?php if (is_adminuser() or $allowzoneadd === TRUE) { ?>
                {
                    icon: 'jtable/lib/themes/metro/add.png',
                    text: 'Import a new zone',
                    click: function() {
                        $('#ImportZone').jtable('showCreateForm');
                    }
                },
                {
                    icon: 'jtable/lib/themes/metro/add.png',
                    text: 'Clone a zone',
                    click: function() {
                        $('#CloneZone').jtable('showCreateForm');
                    }
                },
                <?php } ?>
                ],
        },
        sorting: false,
        openChildAsAccordion: true,
        actions: {
            listAction: 'zones.php?action=list',
            <?php if (is_adminuser() or $allowzoneadd === TRUE) { ?>
            createAction: 'zones.php?action=create',
            deleteAction: 'zones.php?action=delete',
            <?php } ?>
            <?php if (is_adminuser()) { ?>
            updateAction: 'zones.php?action=update'
            <?php } ?>
        },
        fields: {
            id: {
                key: true,
                type: 'hidden'
            },
            name: {
                title: 'Domain',
                width: '8%',
                display: displayContent('name'),
                edit: false,
                inputClass: 'domain',
                listClass: 'domain'
            },
            dnssec: {
                title: 'DNSSEC',
                width: '3%',
                create: false,
                edit: false,
                display: displayDnssecIcon,
                listClass: 'dnssec'
            },
            <?php if (is_adminuser()) { ?>
            account: {
                title: 'Account',
                width: '8%',
                display: displayContent('account'),
                options: function(data) {
                    return 'users.php?action=listoptions&e='+$epoch;
                },
                defaultValue: 'admin',
                inputClass: 'account',
                listClass: 'account'
            },
            <?php } ?>
            kind: {
                title: 'Type',
                width: '20%',
                display: displayContent('kind'),
                options: {'Native': 'Native', 'Master': 'Master'},
                defaultValue: '<?php echo $defaults['defaulttype']; ?>',
                edit: false,
                inputClass: 'kind',
                listClass: 'kind'
            },
            template: {
                title: 'Template',
                options: <?php echo json_encode(user_template_names()); ?>,
                list: false,
                create: true,
                edit: false,
                inputClass: 'template'
            },
            nameserver: {
                title: 'Nameservers',
                create: true,
                list: false,
                edit: false,
                input: function(data) {
                    var $template = data.form.find('#Edit-template');
                    var ns_form = '<?php foreach($defaults['ns'] as $ns) echo '<input type="text" name="nameserver[]" value="'.$ns.'" /><br />'; ?>';
                    var $elem = $('<div id="nameservers">' + ns_form + '</div>');
                    $template.change(function() {
                        $.get('zones.php?action=getformnameservers&template='+$template.val(), function(getdata) {
                            if (getdata != "") {
				$("#nameservers").html(getdata);
                            } else {
                                $("#nameservers").html(ns_form);
                            }
                        });
                    });
                    return $elem;
                },
                inputClass: 'nameserver nameserver1'
            },
            serial: {
                title: 'Serial',
                width: '10%',
                display: displayContent('serial'),
                create: false,
                edit: false,
                inputClass: 'serial',
                listClass: 'serial'
            },
            records: {
                width: '5%',
                title: 'Records',
                edit: false,
                create: false,
                display: function (zone) {
                    var $img = $('<img class="list" src="img/list.png" title="Records" />');
                    $img.click(function () {
                        $('#MasterZones').jtable('openChildTable',
                            $img.closest('tr'), {
                                title: 'Records in ' + zone.record.name,
                                messages: {
                                    addNewRecord: 'Add to ' + zone.record.name,
                                    noDataAvailable: 'No records for ' + zone.record.name
                                },
                                paging: true,
                                sorting: true,
                                pageSize: 20,
                                openChildAsAccordion: true,
                                actions: {
                                    listAction: 'zones.php?action=listrecords&zoneid=' + zone.record.id,
                                    createAction: 'zones.php?action=createrecord&zoneid=' + zone.record.id,
                                    deleteAction: 'zones.php?action=deleterecord&zoneid=' + zone.record.id,
                                    updateAction: 'zones.php?action=editrecord&zoneid=' + zone.record.id
                                },
                                fields: {
                                    domid: {
                                        create: true,
                                        type: 'hidden',
                                        defaultValue: zone.record.id
                                    },
                                    id: {
                                        key: true,
                                        type: 'hidden',
                                        create: false,
                                        edit: false,
                                        list: false
                                    },
                                    domain: {
                                        create: true,
                                        type: 'hidden',
                                        defaultValue: zone.record.name
                                    },
                                    name: {
                                        title: 'Label',
                                        width: '7%',
                                        sorting: true,
                                        create: true,
                                        display: displayContent('name', zone.record.name),
                                        inputClass: 'name',
                                        listClass: 'name'
                                    },
                                    type: {
                                        title: 'Type',
                                        width: '2%',
                                        options: function() {
                                            zonename = new String(zone.record.name);
                                            if (zonename.match(/(\.in-addr|\.ip6)\.arpa/)) {
                                                return {
                                                    'PTR': 'PTR',
                                                    'NS': 'NS',
                                                    'MX': 'MX',
                                                    'TXT': 'TXT',
                                                    'SOA': 'SOA',
                                                    'A': 'A',
                                                    'AAAA': 'AAAA',
                                                    'CERT': 'CERT',
                                                    'CNAME': 'CNAME',
                                                    'LOC': 'LOC',
                                                    'NAPTR': 'NAPTR',
                                                    'SPF': 'SPF',
                                                    'SRV': 'SRV',
                                                    'SSHFP': 'SSHFP',
                                                    'TLSA': 'TLSA',
                                                };
                                            }
                                            return {
                                                'A': 'A',
                                                'AAAA': 'AAAA',
                                                'CERT': 'CERT',
                                                'CNAME': 'CNAME',
                                                'LOC': 'LOC',
                                                'MX': 'MX',
                                                'NAPTR': 'NAPTR',
                                                'NS': 'NS',
                                                'PTR': 'PTR',
                                                'SOA': 'SOA',
                                                'SPF': 'SPF',
                                                'SRV': 'SRV',
                                                'SSHFP': 'SSHFP',
                                                'TLSA': 'TLSA',
                                                'TXT': 'TXT',
                                            };
                                        },
                                        display: displayContent('type'),
                                        create: true,
                                        inputClass: 'type',
                                        listClass: 'type'
                                    },
                                    content: {
                                        title: 'Content',
                                        width: '30%',
                                        create: true,
                                        sorting: true,
                                        display: displayContent('content'),
                                        inputClass: 'content',
                                        listClass: 'content'
                                    },
                                    ttl: {
                                        title: 'TTL',
                                        width: '2%',
                                        create: true,
                                        sorting: false,
                                        display: displayContent('ttl'),
                                        defaultValue: '<?php echo $defaults['ttl']; ?>',
                                        inputClass: 'ttl',
                                        listClass: 'ttl'
                                    },
                                    setptr: {
                                        title: 'Set PTR Record',
                                        width: '2%',
                                        list: false,
                                        create: true,
                                        defaultValue: 'false',
                                        inputClass: 'setptr',
                                        listClass: 'setptr',
                                        options: function() {
                                            return {
                                                '0': 'No',
                                                '1': 'Yes',
                                            };
                                        },
                                    },
                                    disabled: {
                                        title: 'Disabled',
                                        width: '2%',
                                        create: true,
                                        sorting: false,
                                        display: displayContent('disabled'),
                                        defaultValue: '<?php echo $defaults['disabled'] ? 'No' : 'Yes'; ?>',
                                        inputClass: 'disabled',
                                        listClass: 'disabled',
                                        options: function() {
                                            return {
                                                '0': 'No',
                                                '1': 'Yes',
                                            };
                                        },
                                    },
                                }
                            }, function (data) {
                                data.childTable.jtable('load');
                            })
                    });
                    return $img;
                }
            },
            exportzone: {
                title: '',
                width: '1%',
                create: false,
                edit: false,
                display: displayExportIcon,
                listClass: 'exportzone'
            }
        }
    });
    $('#ImportZone').jtable({
        title: 'Import zone',
        actions: {
            createAction: 'zones.php?action=create'
        },
        fields: {
            id: {
                key: true,
                type: 'hidden'
            },
            name: {
                title: 'Domain',
                inputClass: 'domain'
            },
            <?php if (is_adminuser()) { ?>
            account: {
                title: 'Account',
                options: function(data) {
                    return 'users.php?action=listoptions&e='+$epoch;
                },
                defaultValue: 'admin',
                inputClass: 'account'
            },
            <?php } ?>
            kind: {
                title: 'Type',
                options: {'Native': 'Native', 'Master': 'Master'},
                defaultValue: '<?php echo $defaults['defaulttype']; ?>',
                edit: false,
                inputClass: 'type'
            },
            zone: {
                title: 'Zonedata',
                type: 'textarea',
                inputClass: 'zonedata'
            },
            owns: {
                title: 'Overwrite Nameservers',
                type: 'checkbox',
                values: {'0': 'No', '1': 'Yes'},
                defaultValue: 1,
                inputClass: 'overwrite_namerserver'
            },
            nameserver: {
                title: 'Nameservers',
                create: true,
                list: false,
                edit: false,
                input: function(data) {
                    var ns_form = '<?php foreach($defaults['ns'] as $ns) echo '<input type="text" name="nameserver[]" value="'.$ns.'" /><br />'; ?>';
                    var $elem = $('<div id="nameservers">' + ns_form + '</div>');
                    return $elem;
                },
                inputClass: 'nameserver nameserver1'
            },
        },
        recordAdded: function() {
            $("#MasterZones").jtable('load');
            $("#SlaveZones").jtable('load');
        }

    });

    $('#CloneZone').jtable({
        title: 'Clone zone',
        actions: {
            createAction: 'zones.php?action=clone'
        },
        fields: {
            id: {
                key: true,
                type: 'hidden'
            },
            sourcename: {
                title: 'Source domain',
                options: function(data) {
                    return 'zones.php?action=formzonelist&e='+$epoch;
                },
                inputClass: 'sourcename'
            },
            destname: {
                title: 'Domain',
                inputClass: 'destname'
            },
            account: {
                title: 'Account',
                options: function(data) {
                    return 'users.php?action=listoptions&e='+$epoch;
                },
                defaultValue: 'admin',
                inputClass: 'account'
            },
            kind: {
                title: 'Type',
                options: {'Native': 'Native', 'Master': 'Master'},
                defaultValue: '<?php echo $defaults['defaulttype']; ?>',
                edit: false,
                inputClass: 'type'
            },
        },
        recordAdded: function() {
            $("#MasterZones").jtable('load');
            $("#SlaveZones").jtable('load');
        }

    });

    $('#domsearch').addClear({
        onClear: function() { $('#MasterZones').jtable('load'); }
    });

    function searchDoms() {
        $('#MasterZones').jtable('load', {
            domsearch: $('#domsearch').val()
        });
        $('#SlaveZones').jtable('load', {
            domsearch: $('#domsearch').val()
        });
    }

    stimer = 0;

    $('#changepw1, #changepw2').on('input', function(e) {
        if ($('#changepw1').val() != $('#changepw2').val()) {
            $('#changepwsubmit').prop("disabled",true);
        } else {
            $('#changepwsubmit').prop("disabled",false);
        }
    });

    $('#domsearch').on('input', function (e) {
        e.preventDefault();
        clearTimeout(stimer);
        stimer = setTimeout(searchDoms, 400);
    });

    <?php if (is_adminuser()) { ?>
    $('#Users').hide();
    $('#AboutMe').hide();
    $('#aboutme').click(function () {
        $('#Users').hide();
        $('#MasterZones').hide();
        $('#SlaveZones').hide();
        $('#AboutMe').show();
    });
    $('#useradmin').click(function () {
        $('#Users').show();
        $('#MasterZones').hide();
        $('#SlaveZones').hide();
        $('#AboutMe').hide();
    });
    $('#zoneadmin').click(function () {
        $('#Users').hide();
        $('#AboutMe').hide();
        $('#MasterZones').show();
        $('#SlaveZones').show();
    });
    $('#Users').jtable({
        title: 'Users',
        paging: true,
        pageSize: 20,
        sorting: false,
        actions: {
            listAction: 'users.php?action=list',
            createAction: 'users.php?action=create',
            deleteAction: 'users.php?action=delete',
            updateAction: 'users.php?action=update'
        },
        messages: {
            addNewRecord: 'Add new user',
            deleteConfirmation: 'This user will be deleted. Are you sure?'
        },
        fields: {
            id: {
                key: true,
                type: 'hidden'
            },
            emailaddress: {
                title: 'User',
                display: displayContent('emailaddress'),
                inputClass: 'emailaddress',
                listClass: 'emailaddress'
            },
            password: {
                title: 'Password',
                type: 'password',
                list: false,
                inputClass: 'password',
            },
            isadmin: {
                title: 'Admin',
                type: 'checkbox',
                values: {'0': 'No', '1': 'Yes'},
                inputClass: 'isadmin',
                listClass: 'isadmin'
            }
        },
        recordAdded: function() {
            $epoch = getEpoch();
            $("#MasterZones").jtable('reload');
            $("#SlaveZones").jtable('reload');
        }
    });
    $('#Users').jtable('load');
    <?php } ?>
    $('#MasterZones').jtable('load');
    $('#SlaveZones').jtable('load');
});
</script>
</body>
</html>
