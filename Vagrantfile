# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "ffuenf/ubuntu-14.10-server-amd64"
  config.vm.box_url = "https://s3.eu-central-1.amazonaws.com/ffuenf-vagrantboxes/ubuntu/ubuntu-14.10-server-amd64_virtualbox.box"
  config.vm.network :forwarded_port, :guest => 80, :host => 8080
  config.vm.synced_folder ".", "/vagrant", :group => "www-data"
  config.vm.provision :shell, :path => "bootstrap.sh"
end
