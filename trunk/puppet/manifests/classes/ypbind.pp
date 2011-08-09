class ypbind {
  
  package {
    nis:
      ensure       => latest,
      require      => File["/var/cache/debconf/nis.seeds"],
      responsefile => "/var/cache/debconf/nis.seeds",
      #this nis.seeds file is used to answer install questions in this case to set the domain to fudge
  }

  file { "/var/cache/debconf/nis.seeds":
    #this file was produced based on http://projects.puppetlabs.com/projects/1/wiki/Debian_Preseed_Patterns
    source => "puppet:///files/nis.seeds",
    ensure => present;
  }

 

  service {
    nis:
      enable => true,
      ensure => true,
      hasrestart => true,
      #nis has no status command so all ways stops and starts
      #try looking at pattern => as way of getting it to look in the proc table
      restart => "/etc/init.d/nis restart",
      subscribe => [ File["/etc/yp.conf"], File["/etc/nsswitch.conf"] ],
      require => [Package["nis"]];
    }

  file {
    "/etc/yp.conf":
      mode => 644,
      owner => root,
      group => root,
      ensure => file,
      content => template("nis/yp.conf.erb"),
      require => Package["nis"];
    
    "/etc/nsswitch.conf":
      mode => 644,
      owner => root,
      group => root,
      ensure => file,
      content => template("nis/nsswitch.conf.erb");
    }

# I think the nis.seeds deals with this now
#  exec { 
#    "ypdomainname fudge": 
#      path => "/usr/bin:/usr/sbin:/bin",
#      require => Package["nis"];
#    }

  package{nfs-common:
	ensure => latest;
  }
	
  service {
    nfs-common	:
      enable => true,
      ensure => true,
      hasrestart => true,
      restart => "/etc/init.d/nfs-common restart",
      hasstatus => false,
      #status message is non-standard "all daemons running" so it always stops and starts
      require => [Package["nis"], Package["nfs-common"]];
    }

  file {"/media/backup":
     ensure=>directory,
     #if doesnt work without this mode we will have to put a test to see if it exists first
     #mode => 766,
     owner => root,
     group => root,
    }

file {"/etc/pam.d/su":
     source => "puppet:///files/etc/pam.d/su",
     ensure => present,
     mode => 644,
     owner => root,
     group => root,
    }

  exec { 
    "pam_su_chattr": 
      command => "chattr +i /etc/pam.d/su",
      path => "/usr/bin:/usr/sbin:/bin",
      require => File["/etc/pam.d/su"],
    }



  mount{"/home":
	device => "192.168.1.2:/home",
	fstype => nfs,
	ensure => mounted,
	options=> "rsize=8192,wsize=8192,intr,timeo=14,vers=3",
	#may have to use a nolocks option and is that vers=-3 right
	require=> [Package["nis"], Service["nfs-common"]],
        #target=>"/etc/fstab"
     }

  mount{"/media/backup":
	device => "192.168.1.2:/mnt/backup", 
        fstype => nfs,
	ensure => mounted,
        options=> "rsize=8192,wsize=8192,intr,timeo=14,vers=3",
	require=> [File["/media/backup"], Package["nis"], Service["nfs-common"]],
        #target=>"/etc/fstab"

    }


file {"/etc/gdm3/greeter.gconf-defaults":
     ensure  => present,
     mode    => 644,
     owner   => root,
     group   => root,
     source  => "puppet:///files/etc/gdm3/greeter.gconf-defaults";



"/usr/share/icons/gnome/scalable/places/accessspace.svg":
     ensure  => present,
     mode    => 644,
     owner   => root,
     group   => root,
     source  => "puppet:///files/usr/share/icons/gnome/scalable/places/accessspace.svg";


    }




}
