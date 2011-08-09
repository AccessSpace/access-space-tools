#
# SSH Config Distribution
#

class medialab {


	file {"/etc/apt/sources.list":
        	owner   => root,
        	group   => root,
	        mode    => 664,
        	notify  => service[ssh],
        	source  => "puppet:///files/etc/apt/sources.list",
        	ensure  => present,
		require => [Package["debian-multimedia-keyring"]]
	 }

	file {"/var/cache/apt/archives/debian-multimedia-keyring_2010.12.26_all.deb":
        	owner   => root,
        	group   => root,
	        mode    => 664,
        	notify  => service[ssh],
        	source  => "puppet:///files/debian-multimedia-keyring_2010.12.26_all.deb",
        	ensure  => present
	 }

	package{debian-multimedia-keyring:

		provider => dpkg,
                source   => "/var/cache/apt/archives/debian-multimedia-keyring_2010.12.26_all.deb",
		require  => [File["/var/cache/apt/archives/debian-multimedia-keyring_2010.12.26_all.deb"]]
	}
	
	exec{"aptitude update":
		path => "/usr/bin:/usr/sbin:/bin",
		 require => [Package["debian-multimedia-keyring"], file["/etc/apt/sources.list"]]
		}

#  service {
#    gdm3	:
#      enable => true,
#      ensure => true,
#      hasrestart => true,
#      restart => "/etc/init.d/gdm3 restart",
#    }

	 package {[
		chromium,
		jedit,
		kde,
		blender,
		arduino,
		audacity,
                vlc,
                stellarium,
                scribus,
                dialog,
                rwho,
                mc,
                ssh,
		cron-apt,
		ntp,
		gftp,
		openclipart,
		"openclipart-openoffice.org",
		swfmill,
		rhino,
		iceweasel-firebug,
		"openoffice.org-impress",
		eclipse,
		git,
  	        ntpdate,
	        pv,
                lshw,
                vim,
                icedove,
		hugin, 
		flashplugin-nonfree,  
		libdvdcss2,
		msttcorefonts, 
		subversion,
		inkscape,
		kicad,
		php5-cli,
		expect,
		#mkisofs,
		zsh,
		sudo,
		abiword,
		avr-libc,
		avrdude,
		brasero,
		dia,
		eagle,
		exuberant-ctags,
		graphviz,
		mercurial,
		#netbeans-ide,
		#opera,
		ttf-arphic-uming,
		ttf-baekmuk,
		ttf-bengali-fonts,
		ttf-devanagari-fonts,
		ttf-gujarati-fonts,
		ttf-indic-fonts,
		ttf-kannada-fonts,
		ttf-lyx,
		ttf-malayalam-fonts,
		ttf-oriya-fonts,
		ttf-punjabi-fonts,
		ttf-sil-abyssinica,
		ttf-sil-gentium,
		ttf-sil-gentium-basic,
		ttf-tamil-fonts,
		ttf-telugu-fonts,
		unrar
	
	
		]:
		ensure	=> latest,
		require => [Package["debian-multimedia-keyring"]]
	}

	if ($::kernelrelease =~ /.*64/){
		package {[w64codecs]:
		 	ensure  => latest,
         		require => [Package["debian-multimedia-keyring"]]
		}

	}
	else
	{
		package {[w32codecs]:
		 	ensure  => latest,
         		require => [Package["debian-multimedia-keyring"]]
		}
	
	}
	
	#TODO : ADD skype,

	package {[
		epiphany-browser,
		update-notifier,
		samba
		]:
		ensure  => absent,
	}




	file {"/etc/gconf/gconf.xml.defaults/%gconf-tree.xml":
        	owner   => root,
        	group   => root,
	        mode    => 664,
        	#notify  => service[gdm3],
        	source  => "puppet:///files/etc/gconf/gconf.xml.defaults/%gconf-tree.xml",
        	ensure  => present
	 }
    
}
