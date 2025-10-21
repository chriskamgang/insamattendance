@if ($paginator->hasPages())
    <div class="row">
        <div class="dataTables_paginate">
            <ul class="pagination">
                @if ($paginator->onFirstPage())
                    <li class=" paginate_button page-item active "><a class="page-link" >Previous</a>
                    </li>
                @else
                    <li class="paginate_button page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" >Previous</a>
                    </li>
                @endif


                @foreach ($elements as $element)

                    @if (is_string($element))
                        <li class=" paginate_button page-item disabled"><a class="page-link" >{{ $element }}</a></li>
                    @endif



                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="paginate_button page-item active"><a class="page-link">{{ $page }}</a></li>
                            @else
                                <li class="paginate_button page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <li class="paginate_button page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Next →</a>
                    </li>
                @else
                    <li class=" paginate_button page-item active"><a class="page-link">Next →</a></li>
                @endif
            </ul>
        </div>
    </div>

@endif
