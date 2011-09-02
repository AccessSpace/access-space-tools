import "classes/*.pp"

$extlookup_datadir = "/etc/puppet/manifests/extdata"
$extlookup_precedence = ["yphosts"]


node default {

    include dist
	
    if ($::lsbdistrelease == "testing")
    {	
        include ipaddress    
    	include puppet
    	include ssh
    	include ypbind
    	include medialab
    }

}
