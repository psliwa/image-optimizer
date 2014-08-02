# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

#pngout

optipngVersion = "0.7.5"

$provision = <<PROVISION
apt-get update
apt-get install -y php5 phpunit php5-gd
apt-get install -y advancecomp pngcrush gifsicle jpegoptim
apt-get install -y libjpeg-progs libjpeg8-dbg libimage-exiftool-perl
apt-get install -y imagemagick pngnq tar unzip libpng-dev git

#compile pngquant
git clone https://github.com/pornel/pngquant.git && cd ./pngquant && git checkout 2.0.2 && make && ln -s /home/vagrant/pngquant/pngquant /usr/bin/pngquant

cd /home/vagrant

#compile optipng
wget http://downloads.sourceforge.net/project/optipng/OptiPNG/optipng-#{optipngVersion}/optipng-#{optipngVersion}.tar.gz && tar xvf optipng-#{optipngVersion}.tar.gz && cd ./optipng-#{optipngVersion} && ./configure && make && ln -s /home/vagrant/optipng-#{optipngVersion}/src/optipng/optipng /usr/bin/optipng


PROVISION

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "ubuntu/trusty32"

  config.vm.provision "shell", inline: $provision
end
