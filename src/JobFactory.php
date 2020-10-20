<?php



namespace Ivvy;

use Ivvy\Model\Company;
use Ivvy\Model\Contact;
use Ivvy\Model\Validator\Validator;

/**
 * Class: JobFactory
 */
class JobFactory
{
    /** Validators */
    protected $addCompanyValidator;
    protected $updateCompanyValidator;
    protected $addContactValidator;
    protected $updateContactValidator;

    public function __construct(
        Validator $addCompanyValidator,
        Validator $updateCompanyValidator,
        Validator $addContactValidator,
        Validator $updateContactValidator
    ) {
        $this->addCompanyValidator    = $addCompanyValidator;
        $this->updateCompanyValidator = $updateCompanyValidator;
        $this->addContactValidator    = $addContactValidator;
        $this->updateContactValidator = $updateContactValidator;
    }

    /**
     * Creates a job for the ping endpoint
     *
     * @return Job
     */
    public function newPingJob()
    {
        return new Job('test', 'ping');
    }

    public function newAddCompanyJob(Company $company)
    {
        $company->validate($this->addCompanyValidator);

        return $this->newAddOrUpdateCompanyJob($company);
    }

    public function newUpdateCompanyJob(Company $company)
    {
        $company->validate($this->updateCompanyValidator);

        return $this->newAddOrUpdateCompanyJob($company);
    }

    protected function newAddOrUpdateCompanyJob(Company $company)
    {
        return new Job('contact', 'addOrUpdateCompany', $company->toArray(true));
    }

    public function newAddContactJob(Contact $contact)
    {
        $contact->validate($this->addContactValidator);

        return $this->newAddOrUpdateContactJob($contact);
    }

    public function newUpdateContactJob(Contact $contact)
    {
        $contact->validate($this->updateContactValidator);

        return $this->newAddOrUpdateContactJob($contact);
    }

    protected function newAddOrUpdateContactJob(Contact $contact)
    {
        return new Job('contact', 'addOrUpdateContact', $contact->toArray(true));
    }
}
