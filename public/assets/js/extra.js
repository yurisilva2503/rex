$.fn.dataTable.Buttons.defaults.dom.button.className =
    "btn btn-sm";
moment.locale('pt-br');

window.addEventListener("beforeprint", (event) => {
  alert("A impressão está prestes a começar!");
});

async function refreshTableData(table, endpoint) {
    try {
        const response = await fetch(endpoint, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            }
        });

        if (!response.ok) throw new Error('Erro ao atualizar dados da tabela');

        const data = await response.json();
        if (table) {
            table.clear().rows.add(data).draw();
        }
    } catch (error) {
        console.error('Erro ao atualizar tabela:', error);
    }
}
