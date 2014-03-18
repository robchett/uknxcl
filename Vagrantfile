VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "live"
  config.vm.box_url = "https://github.com/2creatives/vagrant-centos/releases/download/v6.5.1/centos65-x86_64-20131205.box"

  config.vm.network :private_network, ip: "192.168.0.10"

  config.vm.synced_folder "..", "/var/www/vhosts/uknxcl"
  config.vm.synced_folder "../conf", "/etc/nginx/conf.d"
  config.vm.provision :shell, :path => "init.sh"

end
