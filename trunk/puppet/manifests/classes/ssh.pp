#
# SSH Config Distribution
#

class ssh {
    service {"ssh":
	name	=> "ssh",
	hasrestart => true,
	restart	=> "/etc/init.d/ssh restart",
	ensure	=> true,
	}

    file {"/etc/ssh/sshd_config":
	owner	=> root,
	group	=> root,
	mode	=> 644,
	notify	=> service[ssh],
	source	=> "puppet:///files/etc/ssh/sshd_config",
	ensure  => present
    }


    
    file {"/etc/ssh/keys":
	owner	=> root,
	group	=> root,
	mode	=> 664,
	notify	=> service[ssh],
	source	=> "puppet:///files/etc/ssh/keys",
	ensure  => directory

    }    

    
    file {"/etc/ssh/keys/AuthorizedKeys":
	owner	=> root,
	group	=> root,
	mode	=> 664,
	notify	=> service[ssh],
	source	=> "puppet:///files/etc/ssh/keys/AuthorizedKeys",
	ensure  => present

    }    


}
