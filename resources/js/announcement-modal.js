function loadAnnouncementModal() {
    fetch('/api/announcements/modal')
        .then(response => response.json())
        .then(data => {
            if (data.announcement) {
                const announcement = data.announcement;
                const modalId = 'announcement-modal-' + announcement.id;
                
                if (sessionStorage.getItem('announcement-seen-' + announcement.id)) {
                    return;
                }
                
                const modalHtml = `
                    <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}Label" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-${announcement.type} text-white">
                                    <h5 class="modal-title" id="${modalId}Label">${announcement.title}</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ${announcement.content}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                const existingModal = document.getElementById(modalId);
                if (existingModal) {
                    existingModal.remove();
                }
                
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                
                const modalElement = document.getElementById(modalId);
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
                
                sessionStorage.setItem('announcement-seen-' + announcement.id, 'true');
            }
        })
        .catch(error => {
            console.error('Error loading announcement:', error);
        });
}

document.addEventListener('DOMContentLoaded', loadAnnouncementModal);

if (typeof $ !== 'undefined' && $.support.pjax) {
    $(document).on('pjax:end', loadAnnouncementModal);
}
