{
  "name": <?= json_encode("Toolbox - " . ctx()->getCompanyName()) ?>,
  "short_name": "Toolbox",
  "start_url": "<?= appUrl('/') ?>",
  "display": "standalone",
  "background_color": "#fff",
  "description": "Toolbox",
  "icons": [{
    "src": "<?= BASE_HREF ?>images/icons/toolbox48x48.png",
    "sizes": "48x48",
    "type": "image/png"
  }, {
    "src": "<?= BASE_HREF ?>images/icons/toolbox72x72.png",
    "sizes": "72x72",
    "type": "image/png"
  }, {
    "src": "<?= BASE_HREF ?>images/icons/toolbox96x96.png",
    "sizes": "96x96",
    "type": "image/png"
  }, {
    "src": "<?= BASE_HREF ?>images/icons/toolbox144x144.png",
    "sizes": "144x144",
    "type": "image/png"
  }, {
    "src": "<?= BASE_HREF ?>images/icons/toolbox168x168.png",
    "sizes": "168x168",
    "type": "image/png"
  }, {
    "src": "<?= BASE_HREF ?>images/icons/toolbox192x192.png",
    "sizes": "192x192",
    "type": "image/png"
  }]
}