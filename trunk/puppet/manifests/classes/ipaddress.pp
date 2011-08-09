#
# IP address setting inleiu of having proper dhcp 
#

class ipaddress {

	$new_ip  = extlookup($::hostname)

	file {"/etc/network/interfaces":
        	owner   => root,
        	group   => root,
        	mode    => 644,
		content => template("interfaces.erb"),
        	ensure  => present,

    	}

# if we have a different ip to the calculated one do a reboot	

    
}
