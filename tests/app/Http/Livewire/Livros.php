<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Livro;

class Livros extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $nome, $descricao, $preco;
    public $updateMode = false;

    public function render()
    {
		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.livros.view', [
            'livros' => Livro::latest()
						->orWhere('nome', 'LIKE', $keyWord)
						->orWhere('descricao', 'LIKE', $keyWord)
						->orWhere('preco', 'LIKE', $keyWord)
						->paginate(10),
        ]);
    }
	
    public function cancel()
    {
        $this->resetInput();
        $this->updateMode = false;
    }
	
    private function resetInput()
    {		
		$this->nome = null;
		$this->descricao = null;
		$this->preco = null;
    }

    public function store()
    {
        $this->validate([
		'nome' => 'required',
		'descricao' => 'required',
		'preco' => 'required',
        ]);

        Livro::create([ 
			'nome' => $this-> nome,
			'descricao' => $this-> descricao,
			'preco' => $this-> preco
        ]);
        
        $this->resetInput();
		$this->emit('closeModal');
		session()->flash('message', 'Livro Successfully created.');
    }

    public function edit($id)
    {
        $record = Livro::findOrFail($id);

        $this->selected_id = $id; 
		$this->nome = $record-> nome;
		$this->descricao = $record-> descricao;
		$this->preco = $record-> preco;
		
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
		'nome' => 'required',
		'descricao' => 'required',
		'preco' => 'required',
        ]);

        if ($this->selected_id) {
			$record = Livro::find($this->selected_id);
            $record->update([ 
			'nome' => $this-> nome,
			'descricao' => $this-> descricao,
			'preco' => $this-> preco
            ]);

            $this->resetInput();
            $this->updateMode = false;
			session()->flash('message', 'Livro Successfully updated.');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            $record = Livro::where('id', $id);
            $record->delete();
        }
    }
}
