# Remote Media Resolver
WordPress plugin used for routing media library asset URLs to a live production URL so that the media library need not exist locally.    
A redirect occurs when the file being referenced by the requested URL would have resulted in a 404. It is instead routed to the specified remote host using the same URL path that was originally requested.

**The Remote Host URL must be specified in `Settings > Remote Media Resolver`.**  
An empty Remote Host URL will **disable** the redirection.

### Example:
Requested URL  
`https://chief-bcc.lndo.site/wp-content/uploads/2019/02/image-1080x600.png`  
Redirects to:  
`https://u.group/wp-content/uploads/2019/02/image-1080x600.png`
