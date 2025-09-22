import $ from 'jquery';
import * as ToastModule from 'bootstrap/js/src/toast';

$(document).ready(function () {
    let lastCheck = new Date().toISOString();
    let interval = 60000; // start with 1 min
    const maxInterval = 300000; // 5 min max

    function checkNewPosts() {
        let url = `${localize.resturl}?after=${lastCheck}&per_page=1&orderby=date&order=desc&_fields=id,title,link,date`;

        fetch(url, {
            headers: {
                'If-Modified-Since': lastCheck
            }
        })
            .then(response => {
                if (response.status === 304) {
                    // No new posts
                    return [];
                }
                return response.json();
            })
            .then(posts => {
                if (posts.length > 0) {
                    // Show toast for latest post
                    const post = posts[0];
                    $('#newPostLink')
                        .attr('href', post.link)
                        .text(post.title.rendered);

                    let toastEl = document.getElementById('newPostToast');
                    let toast = new ToastModule.default(toastEl, {
                        autohide: true,
                        delay: 60000
                    });
                    toast.show();

                    // Reset interval after new post
                    interval = 60000;
                    lastCheck = new Date(post.date).toISOString();
                } else {
                    // Exponential backoff if no new posts
                    interval = Math.min(interval * 2, maxInterval);
                }
            })
            .catch(err => console.error('Hiba a REST API lekérésnél:', err))
            .finally(() => setTimeout(checkNewPosts, interval));
    }

    // Initial check
    checkNewPosts();
});
