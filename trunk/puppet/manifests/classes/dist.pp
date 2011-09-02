class dist {

  file {"/etc/apt/sources.list":
                owner   => root,
                group   => root,
                mode    => 664,
                #notify  => service[ssh],
                source  => "puppet:///files/etc/apt/sources.list",
                ensure  => present,
                require => [Package["debian-multimedia-keyring"]]
         }

        file {"/var/cache/apt/archives/debian-multimedia-keyring_2010.12.26_all.deb":
                owner   => root,
                group   => root,
                mode    => 664,
                #notify  => service[ssh],
                source  => "puppet:///files/debian-multimedia-keyring_2010.12.26_all.deb",
                ensure  => present
         }

        package{debian-multimedia-keyring:

                provider => dpkg,
                source   => "/var/cache/apt/archives/debian-multimedia-keyring_2010.12.26_all.deb",
                require  => [File["/var/cache/apt/archives/debian-multimedia-keyring_2010.12.26_all.deb"]]
        }

 #       exec{"aptitude update":
 #               path => "/usr/bin:/usr/sbin:/bin",
 #                require => [Package["debian-multimedia-keyring"], file["/etc/apt/sources.list"]]
 #               }

#        exec{"aptitude dist-upgrade -y":
#                path => "/usr/bin:/usr/sbin:/bin",
#                require => [Exec["aptitude update"]]
#                }
#
}
