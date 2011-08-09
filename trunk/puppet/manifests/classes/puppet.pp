#
# Puppet Config Distribution
#

class puppet {

    service {"puppet":
	name	=> "puppet",
	hasrestart => true,
	restart	=> "/etc/init.d/puppet restart",
	ensure	=> true,
        enable => true
	}

    file {"/etc/puppet/puppet.conf":
	owner	=> root,
	group	=> root,
	mode	=> 644,
	notify	=> service[puppet],
	source	=> "puppet:///files/etc/puppet/puppet.conf"
    }

    file {"/etc/default/puppet":
	owner	=> root,
	group	=> root,
	mode	=> 644,
	notify	=> service[puppet],
	source	=> "puppet:///files/etc/default/puppet"
    }
    
    
}
