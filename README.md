# SilverStripe Simple CSP 

Allow for management and injection of CSP headers

## Requirements

* silverstripe/cms ^6
* silverstripe/framework ^6

## Installation

```bash
composer require toastnz/simple-csp
```

## Configuration and usage

Configure rules and enable CSP headers within the CMS.

Note: if enabling 'nonce' support, ensure that your templates use the `$CSPNonceAttr` template global to add the nonce attribute to any inline scripts or styles, e.g.

```html
<script {$CSPNonceAttr}>
    // inline script content
</script>

<style {$CSPNonceAttr}>
    /* inline style content */
</style>
```


