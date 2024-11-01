var add_child = document.getElementById('add-child');
var kids_div = document.getElementById('kids');
add_child.onclick = function(){
    var existing_kids = document.getElementsByClassName('child-age').length;
    const kidnum = existing_kids + 1;
    const h4 = document.createElement('h4');
    h4.appendChild(document.createTextNode('Child ' + kidnum));
    //kids_div.appendChild(h4);
    
    const table = document.createElement('table');
    const name_div = document.createElement('div');
    name_div.style.padding = '10px';
    name_div.appendChild(document.createTextNode('Name'));
    name_div.appendChild(document.createElement('br'));
    const name_field = document.createElement('input');
    name_field.className = 'child-age';
    name_field.name = 'cname_' + kidnum;
    name_field.type = 'text';
    name_field.style.width = '100%';
    name_div.appendChild(name_field);
    const tr = document.createElement('tr');
    tr.style.background = 'transparent';
    const name_td = document.createElement('td');
    name_td.style.width = '100px';
    name_td.appendChild(name_div);
    tr.appendChild(name_td);
    
    //kids_div.appendChild(name_div);
    
    const age_div = document.createElement('div');
    age_div.style.padding = '10px';
    age_div.appendChild(document.createTextNode('Age'));
    age_div.appendChild(document.createElement('br'));
    const age_field = document.createElement('input');
    age_field.className = 'child-age';
    age_field.name = 'age_' + kidnum;
    age_field.type = 'number';
    age_field.style.width = '100%';
    age_div.appendChild(age_field);
    
    const age_td = document.createElement('td');
    age_td.style.width = '75px';
    age_td.appendChild(age_div);
    tr.appendChild(age_td);
    
    //kids_div.appendChild(age_div);
    
    const gender_div = document.createElement('div');
    gender_div.appendChild(document.createTextNode('Gender'));
    gender_div.appendChild(document.createElement('br'));
    const gender_select = document.createElement('select');
    gender_select.style.padding = '10px';
    gender_select.style.borderColor = '#ccc';
    gender_select.style.margin = '10px 0px';
    gender_select.name = "sex_" + kidnum;
    const opt_girl = document.createElement('option');
    opt_girl.value = 'F';
    opt_girl.appendChild(document.createTextNode('Girl'));
    gender_select.appendChild(opt_girl);
    const opt_boy = document.createElement('option');
    opt_boy.value = 'M';
    opt_boy.appendChild(document.createTextNode('Boy'));
    gender_select.appendChild(opt_boy);
    gender_div.appendChild(gender_select);
    
    const gender_td = document.createElement('td');
    gender_td.appendChild(gender_div);
    tr.appendChild(gender_td);
    
    table.appendChild(tr);
    const fieldset = document.createElement('div');
    fieldset.className = 'toydrive-fieldset td-child';
    fieldset.appendChild(h4)
    fieldset.appendChild(table);
    kids_div.appendChild(fieldset);
    return false;
}

var signup_form = document.getElementById('signup-form');
signup_form.onsubmit = async function(e){
    e.preventDefault();
    
    var fd = new FormData(this);
    fd.append('action', 'toydrive_handle_form');
    var resp = await fetch('/wp-admin/admin-ajax.php', {method: 'POST', body: fd});
    var status = await resp.json();
    if(!status.success){
        alert('There was a problem submitting your form. Please try again');
        document.getElementById('td-form-spinner').style.display = 'none';
        return false;
    }
    var response_div = document.getElementById('ToyDrive_form_wrapper');
    response_div.innerHTML = this.dataset.response;
    window.scrollTo({ top: 0, behavior: 'smooth' });
    return false;
}

document.getElementById('td-submit-button').onmousedown = function(){
    document.getElementById('td-form-spinner').style.cssText = 'display:inline;';
}
