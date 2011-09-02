#
# SSH Config Distribution
#

class medialab {

	 package {"task-desktop":
		ensure	=> latest,
		require => [Package["debian-multimedia-keyring"]]
	  }

	 package {[
		chromium,
		jedit,
		less,
		blender,
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
		#gftp,
		#openclipart,
		#"openclipart-openoffice.org",
		swfmill,
		rhino,
		iceweasel-firebug,
		#"openoffice.org-impress",
		git,
  	        ntpdate,
	        #pv,
                lshw,
                vim,
                icedove,
		hugin, 
		flashplugin-nonfree,  
		libdvdcss2,
		msttcorefonts, 
		subversion,
		inkscape,
		#kicad,
		php5-cli,
		#expect,
		#zsh,
		abiword,
		avrdude,
		brasero,
		dia,
		eagle,
		exuberant-ctags,
		graphviz,
		mercurial,
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
		unrar,
		gftp

		#arduino,
		#avr-libc,
		#eclipse,
		#netbeans-ide,
		#opera,

		]:
		ensure	=> latest,
		require => [Package["debian-multimedia-keyring"]]
		#require => [Exec["aptitude dist-upgrade -y"]]

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
		#epiphany-browser,
		update-notifier,
		filezilla,
		#samba
		]:
		ensure  => absent,
	}




	file {"/etc/gconf/gconf.xml.defaults/%gconf-tree.xml":
        	owner   => root,
        	group   => root,
	        mode    => 664,
        	#notify  => service[gdm3],
        	source  => "puppet:///files/etc/gconf/gconf.xml.defaults/%gconf-tree.xml",
        	ensure  => present,
		require => [Package["task-desktop"]],

	 }


 cron { logoff:
   command => "service gdm3 restart",
   user => root,
   hour => 19,
   minute => 0
 }


}
