/*
 * document-ready init
 */
$(function() {
	$('.accHandle').on('click', accClick);
});

/**
 * toggle the accordion body
 *
 * grabbs the target-id from data-toggle
 */
function accClick() {
	var target = $(this).data('toggle');

	$('#'+target).slideToggle();
}
