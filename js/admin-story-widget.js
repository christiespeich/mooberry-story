

function mbds_sw_stories_change(stories_dropdown_id, genre_dropdown_id, series_dropdown_id) {
		stories_dropdown = document.getElementById(stories_dropdown_id);
		genre_dropdown = document.getElementById(genre_dropdown_id);
		series_dropdown = document.getElementById(series_dropdown_id);
		
		switch (stories_dropdown.value) {
			case 'series':
				series_dropdown.style.display = 'block';
				genre_dropdown.style.display = 'none';
				break;
			case 'genre':
				series_dropdown.style.display = 'none';
				genre_dropdown.style.display = 'block';
				break;
			default:
				series_dropdown.style.display = 'none';
				genre_dropdown.style.display = 'none';
		}
			
	
}