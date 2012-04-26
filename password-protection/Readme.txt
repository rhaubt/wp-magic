By adding this extra lines of code to the wordpress .htaccess file

RewriteCond %{REQUEST_URI} ^.*uploads/medlemsfiler/.*
RewriteCond %{HTTP_COOKIE} !^.*wordpress_logged_in.*$ [NC]
RewriteRule . /wp-login.php?redirect_to=%{REQUEST_URI} [R,L]

You can protect anny folders content, so that only wordpress users can acsess it!!

and if you use the awsome plugin custum-upload-dir , and sort uploads by posttype
you can create awsome mebersites easely