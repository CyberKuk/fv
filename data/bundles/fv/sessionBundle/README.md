### About
Session bundle user for session handling

## Configuration
Config file path: /data/configs/sessions

##Usage
$session = Bundle\fv\SessionBundle\SessionFactory::get("session name from config");
$session->set($key, $value);
$session->get($key);
