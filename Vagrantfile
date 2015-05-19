# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "scotch/box"
  config.vm.network :forwarded_port, :guest => 80, :host => 8080
  config.vm.synced_folder ".", "/vagrant", :group => "www-data"
  config.vm.provision :shell, :path => "bootstrap.sh"
end
