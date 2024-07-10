$(document).ready(function() {
    loadActions();

    $('#investimento').mask('000.000.000.000.000,00', {reverse: true});

    let editMode = false;
    let editId = null;
    let sortField = '';
    let sortOrder = 'asc';

    $("#marketingForm").on("submit", function(event) {
        event.preventDefault();

        let acao = $("#acao").val();
        let data = $("#data").val();
        let investimento = $("#investimento").val();
        investimento = investimento.replace(/\./g, '').replace(',', '.');

        let dataPrevista = new Date(data + 'T00:00:00');
        let dataAtualMais10Dias = new Date();
        dataAtualMais10Dias.setDate(dataAtualMais10Dias.getDate() + 10);

        if (dataPrevista < dataAtualMais10Dias) {
            alert("A data mínima é de 10 dias a partir da data de cadastro.");
            return;
        }

        const postData = {acao: acao, data: data, investimento: investimento, action: editMode ? 'edit' : 'add'};
        if (editMode) {
            postData.id = editId;
        }

        $.ajax({
            url: 'backend/actions.php',
            type: 'POST',
            data: postData,
            success: function(response) {
                loadActions();
                $("#marketingForm")[0].reset();
                editMode = false;
                editId = null;
            },
            error: function(xhr, status, error) {
                console.error("Erro ao adicionar/editar ação:", error);
                console.error("Detalhes:", xhr.responseText);
            }
        });
    });

    $("#limpar").on("click", function() {
        $("#marketingForm")[0].reset();
        editMode = false;
        editId = null;
    });

    function formatDate(dateStr) {
        let date = new Date(dateStr + 'T00:00:00');
        let day = String(date.getUTCDate()).padStart(2, '0');
        let month = String(date.getUTCMonth() + 1).padStart(2, '0');
        let year = date.getUTCFullYear();
        return `${year}-${month}-${day}`;
    }

    function formatDisplayDate(dateStr) {
        let date = new Date(dateStr + 'T00:00:00');
        let day = String(date.getUTCDate()).padStart(2, '0');
        let month = String(date.getUTCMonth() + 1).padStart(2, '0');
        let year = date.getUTCFullYear();
        return `${day}/${month}/${year}`;
    }

    function loadActions() {
        $.ajax({
            url: 'backend/actions.php',
            type: 'POST',
            data: {action: 'list'},
            success: function(response) {
                let actions = JSON.parse(response);
                if (sortField) {
                    actions.sort((a, b) => {
                        if (sortField === 'data') {
                            return sortOrder === 'asc' ? new Date(a[sortField]) - new Date(b[sortField]) : new Date(b[sortField]) - new Date(a[sortField]);
                        } else if (sortField === 'investimento') {
                            return sortOrder === 'asc' ? parseFloat(a[sortField]) - parseFloat(b[sortField]) : parseFloat(b[sortField]) - parseFloat(a[sortField]);
                        } else {
                            return sortOrder === 'asc' ? a[sortField].localeCompare(b[sortField]) : b[sortField].localeCompare(a[sortField]);
                        }
                    });
                }

                let rows = '';
                actions.forEach(action => {
                    rows += `<tr>
                        <td>${action.acao}</td>
                        <td>${formatDisplayDate(action.data)}</td>
                        <td>R$ ${parseFloat(action.investimento).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td><button class="btn-edit" data-id="${action.id}"><i class="glyphicon glyphicon-pencil"></i></button></td>
                        <td><button class="btn-delete" data-id="${action.id}"><i class="glyphicon glyphicon-remove"></i></button></td>
                    </tr>`;
                });
                $("#acoesTable tbody").html(rows);
            },
            error: function(xhr, status, error) {
                console.error("Erro ao carregar ações:", error);
            }
        });
    }

    $(document).on("click", ".btn-delete", function() {
        let id = $(this).data("id");
        $.ajax({
            url: 'backend/actions.php',
            type: 'POST',
            data: {id: id, action: 'delete'},
            success: function(response) {
                loadActions();
            },
            error: function(xhr, status, error) {
                console.error("Erro ao excluir ação:", error);
            }
        });
    });

    $(document).on("click", ".btn-edit", function() {
        let id = $(this).data("id");
        editMode = true;
        editId = id;

        $.ajax({
            url: 'backend/actions.php',
            type: 'POST',
            data: {id: id, action: 'get'},
            success: function(response) {
                const action = JSON.parse(response);
                if (action && action.length > 0) {
                    $("#acao").val(action[0].acao);
                    $("#data").val(action[0].data);
                    $("#investimento").val(parseFloat(action[0].investimento).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).replace(/\./g, '').replace(',', '.'));
                }
            },
            error: function(xhr, status, error) {
                console.error("Erro ao carregar dados da ação:", error);
                console.error("Detalhes:", xhr.responseText);
            }
        });
    });

    $(document).on("click", ".sortable", function() {
        sortField = $(this).data("sort");
        sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
        loadActions();
        updateSortIcons();
    });

    function updateSortIcons() {
        $(".sortable .glyphicon").removeClass("glyphicon-sort-by-attributes glyphicon-sort-by-attributes-alt");
        $(".sortable").each(function() {
            if ($(this).data("sort") === sortField) {
                if (sortOrder === 'asc') {
                    $(this).find(".glyphicon").addClass("glyphicon-sort-by-attributes");
                } else {
                    $(this).find(".glyphicon").addClass("glyphicon-sort-by-attributes-alt");
                }
            }
        });
    }
});
