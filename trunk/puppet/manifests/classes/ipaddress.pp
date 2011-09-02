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
		require => [Package["network-manager"]],
    	}


	file {"/etc/resolv.conf":
        	owner   => root,
        	group   => root,
        	mode    => 644,
		source  => "puppet:///files/etc/resolv.conf",
        	ensure  => present,
		require => [Package["network-manager"]],

    	}



 package {
    network-manager:
                ensure  => absent,
    require => [Package["task-desktop"]],
        }





# if we have a different ip to the calculated one do a reboot	

    
}
