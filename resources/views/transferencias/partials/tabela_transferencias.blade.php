<table id="example1" class="table table-bordered table-hover">
    <thead>
    <tr>
        <th>Conta {{ $tipo === 'origem' ? 'Destino' : 'Origem' }}</th>
        <th>Data</th>
        <th>Valor</th>
        <th style="width: 110px">Ações</th>
    </tr>
    </thead>
    <tbody>
    @foreach($transferencias as $transferencia)
        <tr>
            <td>
                @if($tipo === 'origem')
                    {{ $transferencia->ContaDestino->Nome . ' - ' . $transferencia->ContaDestino->Banco }}
                @else
                    {{ $transferencia->ContaOrigem->Nome . ' - ' . $transferencia->ContaOrigem->Banco }}
                @endif
            </td>
            <td style="text-align: center">{{ date('d/m/Y', strtotime($transferencia->Data)) }}</td>
            <td>{{ 'R$ ' . number_format($transferencia->Valor, 2, ',', '.') }}</td>
            <td>
                <div class="d-flex justify-content-center align-items-center gap-2 flex-nowrap">
                    <form action="{{ route('transferencias.edit', ['ID_Transferencia' => $transferencia->ID_Transferencia]) }}" method="GET">
                        <button type="submit" class="btn btn-sm btn-primary" title="Editar">
                            <i class="fa fa-edit"></i>
                        </button>
                    </form>
                    <form action="{{ route('transferencias.destroy', ['ID_Transferencia' => $transferencia->ID_Transferencia]) }}" method="POST">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-sm btn-danger" title="Excluir"
                                onclick="return confirm('Deseja realmente excluir este registro?')">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <th>Conta {{ $tipo === 'origem' ? 'Destino' : 'Origem' }}</th>
        <th>Data</th>
        <th>Valor</th>
        <th style="width: 110px">Ações</th>
    </tr>
    </tfoot>
</table>
