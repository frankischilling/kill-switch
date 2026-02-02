# Developer Assurance Protocol

This repository contains a **Developer Assurance / Termination Protocol** designed to permanently disable a PHP/MySQL website in the event of a **contract breach, non-payment, or unauthorized code use**.

This is not a recovery tool.  
This is not a maintenance script.  
**This action is irreversible by design.**

---

## Purpose

The Developer Assurance Protocol exists to provide a **last-resort enforcement mechanism** for developers who retain contractual rights over deployed software.

When triggered, the script:

1. Destroys all critical database data  
2. Deletes the primary configuration file  
3. Overwrites public and admin entry points  
4. Creates a hard lock file preventing restoration  
5. Returns a neutral termination notice  

Once executed, the website is permanently disabled unless rebuilt from backups.

---

## What This Script Does

### Database Actions
- Connects to MySQL using existing credentials
- Disables foreign key checks
- Truncates all defined critical tables
- Resets AUTO_INCREMENT values
- Re-enables foreign key checks

### Filesystem Actions
- Deletes `config.php`
- Overwrites `index.php` with a termination notice
- Overwrites `admin.php` with a termination notice
- Creates `.site_terminated.lock` as a permanent kill flag

### Access Control
- Requires a secret termination key
- Returns HTTP 404 on unauthorized access
- Supports optional IP whitelisting

---

## Example Trigger URL

```

[https://example.com/backend/terminate_site_[random].php?key=YOUR_SECRET_KEY](https://example.com/backend/terminate_site_[random].php?key=YOUR_SECRET_KEY)

```

> The script should be renamed, placed outside public directories when possible, and never referenced anywhere in application code.

---

## Security Notes

- Use a long, unguessable termination key
- Rename the script to a non-descriptive filename
- Keep this repository private
- Never deploy this on shared hosting without explicit permission
- Maintain verified off-site backups

---

## Legal & Contractual Notice

This script is intended to be used **only where explicitly permitted by contract or law**.

The author assumes:
- The developer retains ownership or enforcement rights
- The client has been informed of termination conditions
- Execution complies with applicable local, state, and federal laws

**Improper use may result in irreversible data loss and legal liability.**

---

## Status

- ⚠️ Production-capable  
- ❌ No rollback  
- ❌ No recovery  
- ✅ Explicitly destructive by design  

---

## Final Warning

If you are not prepared to permanently destroy a live system:

**Do not deploy this.  
Do not test this.  
Do not execute this.**
