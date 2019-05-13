<?php

namespace ITHive\SJTickets\API\V1\Interfaces;

interface Ticket
{
    public function getStatuses();

    public function setStatus();
}