import "classes/*.pp"

$extlookup_datadir = "/etc/puppet/manifests/extdata"
$extlookup_precedence = ["yphosts"]


node default {
    include puppet
    include ssh
    include ipaddress
    include ypbind
    include medialab

}
