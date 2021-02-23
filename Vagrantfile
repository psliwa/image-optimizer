# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

#pngout

optipngVersion = "0.7.5"

$provision = <<PROVISION
apt-get update
apt-get install -y php php-gd php-xml php-mbstring
apt-get install -y advancecomp pngcrush gifsicle jpegoptim
apt-get install -y libjpeg-progs libjpeg8-dbg libimage-exiftool-perl
apt-get install -y imagemagick pngnq tar unzip libpng-dev git
apt-get install -y optipng pngquant
apt-get install -y npm nodejs
npm install -g svgo@1.3.2

cd /home/ubuntu

#install pngout

wget http://www.jonof.id.au/files/kenutils/pngout-20150319-linux.tar.gz && tar xvf pngout-20150319-linux.tar.gz

architectures=(x86_64 i686)

for architecture in ${architectures[*]}
do
    ln -s /home/ubuntu/pngout-20150319-linux/$architecture/pngout /usr/bin/pngout

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
  config.vm.box = "ubuntu/focal64"

  config.vm.provision "shell", inline: $provision
end
