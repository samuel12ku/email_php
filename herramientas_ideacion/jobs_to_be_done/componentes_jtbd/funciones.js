/* =========  AGREGAR NUEVO ACTOR  ========= */
document.getElementById('btnAggActor').addEventListener('click', () => {
  const tbody = document.getElementById('tbodyJobs');
  const idx   = tbody.querySelectorAll('tr').length / 3;


  const row1 = document.createElement('tr');
  row1.innerHTML = `
    <td rowspan="3">
      <input type="text" name="actor[]" placeholder="Actor" id="actor" name="actor" required />
    </td>
    <td>
      <input type="text" name="job_1[]" placeholder="Job 1" id="job_1" name="job_1" required />
     
    </td>
  `;

  const row2 = document.createElement('tr');
  row2.innerHTML = `
    <td>
      <input type="text" name="job_2[]" placeholder="Job 2" id="job_2" name="job_2" required />
    </td>
  `;

  const row3 = document.createElement('tr');
  row3.innerHTML = `
    <td>
      <input type="text" name="job_3[]" placeholder="Job 3" required id="job_3" name="job_3"/>
    </td>
  `;

  tbody.appendChild(row1);
  tbody.appendChild(row2);
  tbody.appendChild(row3);
});

/* =========  MENSAJE AL ENVIAR   ========= */
document.getElementById('jtbdForm').addEventListener('submit', e => {
  // validación rápida antes de enviar
  const required = e.target.querySelectorAll('[required]');
  for (let el of required) {
    if (!el.value.trim()) {
      alert('Por favor completa todos los campos.');
      e.preventDefault();
      return;
    }
  }

});