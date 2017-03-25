# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

#pngout

optipngVersion = "0.7.5"

$provision = <<PROVISION
apt-get update
apt-get install -y php php-gd php-dom
apt-get install -y advancecomp pngcrush gifsicle jpegoptim
apt-get install -y libjpeg-progs libjpeg8-dbg libimage-exiftool-perl
apt-get install -y imagemagick pngnq tar unzip libpng-dev git
apt-get install -y optipng pngquant

cd /home/ubuntu

#install pngout
wget http://static.jonof.id.au/dl/kenutils/pngout-20130221-linux-static.tar.gz && tar xvf pngout-20130221-linux-static.tar.gz

architectures=(x86_64 athlon i386 i686 pentium4)

for architecture in ${architectures[*]}
do
    ln -s /home/ubuntu/pngout-20130221-linux-static/$architecture/pngout-static /usr/bin/pngout

    pngout 1> /dev/null 2> /dev/null

    OUT=$?

    if [ $OUT -eq 1 ]
    then
        break
    else
        rm /usr/bin/pngout
    fi
done

cd /home/ubuntu

PROVISION

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "ubuntu/xenial64"

  config.vm.provision "shell", inline: $provision
end
